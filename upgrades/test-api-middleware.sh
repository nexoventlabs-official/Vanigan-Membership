#!/bin/bash

# API Key Middleware Testing Script
# Test the protected endpoints and verify other endpoints still work
# Run this on your trial/staging environment after deploying the changes

BASE_URL="https://vanigan.digital"
ADMIN_KEY="b7f3c9e2a5d1f8c4e6b2a9f1d7e3c5b8a4f9d2e8b1c6f3a7e9d4c1f6b8a5e2"
RESET_KEY="a8e2f1d9c3b6e4a7f2c5d8b1e9a3f6c2d5e8b1a4f7c0d3e6a9b2c5f8e1d4a7"

echo "=========================================="
echo "API Key Middleware Test Suite"
echo "=========================================="
echo ""

# Color codes for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

pass_count=0
fail_count=0

# Helper function to test and report
test_endpoint() {
    local test_name=$1
    local method=$2
    local endpoint=$3
    local headers=$4
    local data=$5
    local expected_status=$6

    echo ""
    echo "TEST: $test_name"
    echo "---"

    if [ -z "$data" ]; then
        response=$(curl -s -w "\n%{http_code}" -X $method "$BASE_URL$endpoint" $headers)
    else
        response=$(curl -s -w "\n%{http_code}" -X $method "$BASE_URL$endpoint" -H "Content-Type: application/json" $headers -d "$data")
    fi

    http_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | head -n-1)

    echo "Request: $method $endpoint"
    if [ ! -z "$headers" ]; then
        echo "Headers: $headers"
    fi
    if [ ! -z "$data" ]; then
        echo "Body: $data"
    fi
    echo "Response Status: $http_code"
    echo "Response Body: $body"

    if [[ "$http_code" == "$expected_status"* ]]; then
        echo -e "${GREEN}✓ PASS${NC} (Expected HTTP $expected_status, got $http_code)"
        ((pass_count++))
    else
        echo -e "${RED}✗ FAIL${NC} (Expected HTTP $expected_status, got $http_code)"
        ((fail_count++))
    fi
}

echo ""
echo "=========================================="
echo "SECTION 1: Protected Endpoint Tests"
echo "=========================================="

# Test 1: Reset Members - Missing Key
test_endpoint \
    "Reset Members - Missing X-Admin-Key" \
    "POST" \
    "/api/vanigam/reset-members" \
    "" \
    '{"confirm_key":"test"}' \
    "401"

# Test 2: Reset Members - Invalid Key
test_endpoint \
    "Reset Members - Invalid X-Admin-Key" \
    "POST" \
    "/api/vanigam/reset-members" \
    "-H 'X-Admin-Key: wrong-key'" \
    '{"confirm_key":"test"}' \
    "401"

# Test 3: Reset Members - Valid Key, Invalid Confirm Key
test_endpoint \
    "Reset Members - Valid API Key, Invalid Confirm Key" \
    "POST" \
    "/api/vanigam/reset-members" \
    "-H 'X-Admin-Key: $ADMIN_KEY'" \
    '{"confirm_key":"wrong-confirm-key"}' \
    "403"

# Test 4: Reset Members - Valid Key, Valid Confirm Key (Will delete if members exist)
test_endpoint \
    "Reset Members - Valid API Key & Valid Confirm Key" \
    "POST" \
    "/api/vanigam/reset-members" \
    "-H 'X-Admin-Key: $ADMIN_KEY'" \
    '{"confirm_key":"'$RESET_KEY'"}' \
    "200"

# Test 5: Upload Card Images - Missing Key
test_endpoint \
    "Upload Card Images - Missing X-Admin-Key" \
    "POST" \
    "/api/vanigam/upload-card-images" \
    "" \
    '{"unique_id":"test"}' \
    "401"

# Test 6: Upload Card Images - Invalid Key
test_endpoint \
    "Upload Card Images - Invalid X-Admin-Key" \
    "POST" \
    "/api/vanigam/upload-card-images" \
    "-H 'X-Admin-Key: invalid-key'" \
    '{"unique_id":"test"}' \
    "401"

echo ""
echo "=========================================="
echo "SECTION 2: Public Endpoint Tests (Should Still Work)"
echo "=========================================="

# Test 7: Check Member (Public endpoint)
test_endpoint \
    "Check Member - Should Work (Public Endpoint)" \
    "POST" \
    "/api/vanigam/check-member" \
    "" \
    '{"epic_no":"TEST123"}' \
    "200"

# Test 8: Send OTP (Public endpoint)
test_endpoint \
    "Send OTP - Should Work (Public Endpoint)" \
    "POST" \
    "/api/vanigam/send-otp" \
    "" \
    '{"epic_no":"TEST123"}' \
    "200"

# Test 9: Validate EPIC (Public endpoint)
test_endpoint \
    "Validate EPIC - Should Work (Public Endpoint)" \
    "POST" \
    "/api/vanigam/validate-epic" \
    "" \
    '{"epic_no":"TEST123"}' \
    "200"

# Test 10: Get Member (Public endpoint)
test_endpoint \
    "Get Member - Should Work (Public Endpoint)" \
    "GET" \
    "/api/vanigam/member/TEST-ID" \
    "" \
    "" \
    "200"

# Test 11: Get QR Code (Public endpoint)
test_endpoint \
    "Get QR Code - Should Work (Public Endpoint)" \
    "GET" \
    "/api/vanigam/qr/TEST-ID" \
    "" \
    "" \
    "200"

# Test 12: Verify PIN (Public endpoint)
test_endpoint \
    "Verify PIN - Should Work (Public Endpoint)" \
    "POST" \
    "/api/vanigam/verify-pin" \
    "" \
    '{"unique_id":"TEST-ID","pin":"1234"}' \
    "200"

# Test 13: Check Loan Status (Public endpoint)
test_endpoint \
    "Check Loan Status - Should Work (Public Endpoint)" \
    "POST" \
    "/api/vanigam/check-loan-status" \
    "" \
    '{"unique_id":"TEST-ID"}' \
    "200"

echo ""
echo "=========================================="
echo "TEST SUMMARY"
echo "=========================================="
echo -e "${GREEN}✓ Passed: $pass_count${NC}"
echo -e "${RED}✗ Failed: $fail_count${NC}"
echo "=========================================="

if [ $fail_count -eq 0 ]; then
    echo -e "${GREEN}ALL TESTS PASSED!${NC}"
    exit 0
else
    echo -e "${RED}SOME TESTS FAILED!${NC}"
    exit 1
fi
