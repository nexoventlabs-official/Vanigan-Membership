<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Log;

echo "=== CLEANUP: Remove specific members from MongoDB + Cloudinary ===\n\n";

// Members to remove
$uniqueIds = [
    'TNVS-323BA979',
    'TNVS-E91CE488',
    'TNVS-FD657BC3',
    'TNVS-423E57E3',
    'TNVS-2E1A290F',
    'TNVS-09B14340',
    'TNVS-0651E7',
    'TNVS-8C43B7',
];

// Connect to main MongoDB
$mongoUrl = config('mongodb.url');
$mongoDb  = config('mongodb.database');
$client   = new MongoDB\Client($mongoUrl);
$db       = $client->selectDatabase($mongoDb);

$membersCol      = $db->selectCollection('members');
$manualCol       = $db->selectCollection('manual_entries');
$loanCol         = $db->selectCollection('loan_requests');

// Connect to tracking MongoDB
$trackingUrl = config('mongodb.tracking_url');
$trackingDb  = config('mongodb.tracking_database');
$trackingClient = null;
$trackingCol    = null;
if (!empty($trackingUrl)) {
    try {
        $trackingClient = new MongoDB\Client($trackingUrl);
        $trackingCol = $trackingClient->selectDatabase($trackingDb)->selectCollection('incomplete_registrations');
        echo "Tracking DB connected.\n";
    } catch (Exception $e) {
        echo "Warning: Could not connect to tracking DB: " . $e->getMessage() . "\n";
    }
}

// Cloudinary
$cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'));

$totalDeleted = 0;

foreach ($uniqueIds as $uid) {
    echo "\n--- Processing: {$uid} ---\n";

    // 1. Find member in main 'members' collection
    $member = $membersCol->findOne(['unique_id' => $uid]);
    if (!$member) {
        echo "  [members] NOT FOUND. Skipping member lookup.\n";
    } else {
        $m = json_decode(json_encode($member), true);
        $mobile  = $m['mobile'] ?? '';
        $epicNo  = $m['epic_no'] ?? '';
        $name    = $m['name'] ?? '';
        $photoUrl = $m['photo_url'] ?? '';

        echo "  [members] Found: {$name} | Mobile: {$mobile} | EPIC: {$epicNo}\n";

        // 2. Delete Cloudinary photo (vanigan/member_photos/{epic_no}_*)
        if (!empty($photoUrl) && !empty($epicNo)) {
            try {
                // Extract public_id from URL
                // URL format: https://res.cloudinary.com/.../vanigan/member_photos/EPICNO_timestamp.jpg
                if (preg_match('/vanigan\/member_photos\/([^\.]+)/', $photoUrl, $matches)) {
                    $photoPublicId = 'vanigan/member_photos/' . $matches[1];
                    $cloudinary->uploadApi()->destroy($photoPublicId);
                    echo "  [cloudinary] Photo deleted: {$photoPublicId}\n";
                } else {
                    echo "  [cloudinary] Could not parse photo public_id from: {$photoUrl}\n";
                }
            } catch (Exception $e) {
                echo "  [cloudinary] Photo delete error: " . $e->getMessage() . "\n";
            }
        }

        // 3. Delete Cloudinary card images (vanigan/cards/{unique_id}/front, /back)
        try {
            $cloudinary->uploadApi()->destroy('vanigan/cards/' . $uid . '/front');
            echo "  [cloudinary] Card front deleted: vanigan/cards/{$uid}/front\n";
        } catch (Exception $e) {
            echo "  [cloudinary] Card front delete: " . $e->getMessage() . "\n";
        }
        try {
            $cloudinary->uploadApi()->destroy('vanigan/cards/' . $uid . '/back');
            echo "  [cloudinary] Card back deleted: vanigan/cards/{$uid}/back\n";
        } catch (Exception $e) {
            echo "  [cloudinary] Card back delete: " . $e->getMessage() . "\n";
        }

        // Try to delete the card folder too
        try {
            $cloudinary->adminApi()->deleteFolder('vanigan/cards/' . $uid);
            echo "  [cloudinary] Card folder deleted: vanigan/cards/{$uid}\n";
        } catch (Exception $e) {
            echo "  [cloudinary] Card folder delete: " . $e->getMessage() . "\n";
        }

        // 4. Delete from members collection
        $result = $membersCol->deleteOne(['unique_id' => $uid]);
        echo "  [members] Deleted: " . $result->getDeletedCount() . " doc(s)\n";

        // 5. Delete from manual_entries (by mobile or unique_id)
        $manualResult = $manualCol->deleteMany([
            '$or' => [
                ['unique_id' => $uid],
                ['mobile' => $mobile],
            ]
        ]);
        echo "  [manual_entries] Deleted: " . $manualResult->getDeletedCount() . " doc(s)\n";

        // 6. Delete from loan_requests (by unique_id or mobile)
        $loanResult = $loanCol->deleteMany([
            '$or' => [
                ['unique_id' => $uid],
                ['mobile' => $mobile],
            ]
        ]);
        echo "  [loan_requests] Deleted: " . $loanResult->getDeletedCount() . " doc(s)\n";

        // 7. Delete from tracking DB (by mobile)
        if ($trackingCol && !empty($mobile)) {
            $trackResult = $trackingCol->deleteMany(['mobile' => $mobile]);
            echo "  [tracking] Deleted: " . $trackResult->getDeletedCount() . " doc(s)\n";
        }

        $totalDeleted++;
    }

    // Also try to find in members by partial unique_id match (for short IDs like TNVS-0651E7)
    if (!$member) {
        $regex = new MongoDB\BSON\Regex('^' . preg_quote($uid, '/'), 'i');
        $partialMatch = $membersCol->findOne(['unique_id' => $regex]);
        if ($partialMatch) {
            $pm = json_decode(json_encode($partialMatch), true);
            $fullUid = $pm['unique_id'] ?? $uid;
            echo "  [members] Found partial match: {$fullUid}\n";
            echo "  Re-run with full ID: {$fullUid}\n";
        }
    }
}

echo "\n=== SUMMARY ===\n";
echo "Processed: " . count($uniqueIds) . " member IDs\n";
echo "Successfully deleted from MongoDB: {$totalDeleted}\n";
echo "DONE.\n";
