# API Key Middleware Testing Script - Windows PowerShell Version
# Test the protected endpoints and verify other endpoints still work

$BASE_URL = "https://phpstack-1603086-6293159.cloudwaysapps.com"
$ADMIN_KEY = "b7f3c9e2a5d1f8c4e6b2a9f1d7e3c5b8a4f9d2e8b1c6f3a7e9d4c1f6b8a5e2"
$RESET_KEY = "a8e2f1d9c3b6e4a7f2c5d8b1e9a3f6c2d5e8b1a4f7c0d3e6a9b2c5f8e1d4a7"

$pass_count = 0
$fail_count = 0

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "API Key Middleware Test Suite" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Helper function to test and report
function Test-Endpoint {
    param(
        [string]$TestName,
        [string]$Method,
        [string]$Endpoint,
        [hashtable]$Headers = @{},
        [string]$Data = "",
        [int]$ExpectedStatus
    )

    Write-Host ""
    Write-Host "TEST: $TestName" -ForegroundColor Yellow
    Write-Host "---"

    $uri = "$BASE_URL$Endpoint"

    try {
        $params = @{
            Uri = $uri
            Method = $Method
            Headers = $Headers
            ContentType = "application/json"
            ErrorAction = "SilentlyContinue"
            SkipHttpErrorCheck = $true
        }

        if ($Data -ne "") {
            $params["Body"] = $Data
        }

        $response = Invoke-WebRequest @params
        $http_code = $response.StatusCode
        $body = $response.Content
    }
    catch {
        $http_code = $_.Exception.Response.StatusCode.value__
        $body = $_.Exception.Response | ConvertFrom-Json | ConvertTo-Json -Compress
    }

    Write-Host "Request: $Method $Endpoint"
    if ($Headers.Count -gt 0) {
        Write-Host "Headers: $(($Headers.GetEnumerator() | ForEach-Object { "$($_.Key): $($_.Value)" }) -join ', ')"
    }
    if ($Data -ne "") {
        Write-Host "Body: $Data"
    }
    Write-Host "Response Status: $http_code"
    Write-Host "Response Body: $body"

    if ($http_code -eq $ExpectedStatus) {
        Write-Host "✓ PASS (Expected HTTP $ExpectedStatus, got $http_code)" -ForegroundColor Green
        return $true
    } else {
        Write-Host "✗ FAIL (Expected HTTP $ExpectedStatus, got $http_code)" -ForegroundColor Red
        return $false
    }
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "SECTION 1: Protected Endpoint Tests" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

# Test 1: Reset Members - Missing Key
if (Test-Endpoint `
    -TestName "Reset Members - Missing X-Admin-Key" `
    -Method "POST" `
    -Endpoint "/api/vanigam/reset-members" `
    -Headers @{} `
    -Data '{"confirm_key":"test"}' `
    -ExpectedStatus 401) {
    $pass_count++
} else {
    $fail_count++
}

# Test 2: Reset Members - Invalid Key
if (Test-Endpoint `
    -TestName "Reset Members - Invalid X-Admin-Key" `
    -Method "POST" `
    -Endpoint "/api/vanigam/reset-members" `
    -Headers @{"X-Admin-Key" = "wrong-key"} `
    -Data '{"confirm_key":"test"}' `
    -ExpectedStatus 401) {
    $pass_count++
} else {
    $fail_count++
}

# Test 3: Reset Members - Valid Key, Invalid Confirm Key
if (Test-Endpoint `
    -TestName "Reset Members - Valid API Key, Invalid Confirm Key" `
    -Method "POST" `
    -Endpoint "/api/vanigam/reset-members" `
    -Headers @{"X-Admin-Key" = $ADMIN_KEY} `
    -Data '{"confirm_key":"wrong-confirm-key"}' `
    -ExpectedStatus 403) {
    $pass_count++
} else {
    $fail_count++
}

# Test 4: Reset Members - Valid Key, Valid Confirm Key
if (Test-Endpoint `
    -TestName "Reset Members - Valid API Key & Valid Confirm Key" `
    -Method "POST" `
    -Endpoint "/api/vanigam/reset-members" `
    -Headers @{"X-Admin-Key" = $ADMIN_KEY} `
    -Data "{`"confirm_key`":`"$RESET_KEY`"}" `
    -ExpectedStatus 200) {
    $pass_count++
} else {
    $fail_count++
}

# Test 5: Upload Card Images - Missing Key
if (Test-Endpoint `
    -TestName "Upload Card Images - Missing X-Admin-Key" `
    -Method "POST" `
    -Endpoint "/api/vanigam/upload-card-images" `
    -Headers @{} `
    -Data '{"unique_id":"test"}' `
    -ExpectedStatus 401) {
    $pass_count++
} else {
    $fail_count++
}

# Test 6: Upload Card Images - Invalid Key
if (Test-Endpoint `
    -TestName "Upload Card Images - Invalid X-Admin-Key" `
    -Method "POST" `
    -Endpoint "/api/vanigam/upload-card-images" `
    -Headers @{"X-Admin-Key" = "invalid-key"} `
    -Data '{"unique_id":"test"}' `
    -ExpectedStatus 401) {
    $pass_count++
} else {
    $fail_count++
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "SECTION 2: Public Endpoint Tests" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

# Test 7: Check Member
if (Test-Endpoint `
    -TestName "Check Member - Should Work (Public Endpoint)" `
    -Method "POST" `
    -Endpoint "/api/vanigam/check-member" `
    -Headers @{} `
    -Data '{"epic_no":"TEST123"}' `
    -ExpectedStatus 200) {
    $pass_count++
} else {
    $fail_count++
}

# Test 8: Send OTP
if (Test-Endpoint `
    -TestName "Send OTP - Should Work (Public Endpoint)" `
    -Method "POST" `
    -Endpoint "/api/vanigam/send-otp" `
    -Headers @{} `
    -Data '{"epic_no":"TEST123"}' `
    -ExpectedStatus 200) {
    $pass_count++
} else {
    $fail_count++
}

# Test 9: Validate EPIC
if (Test-Endpoint `
    -TestName "Validate EPIC - Should Work (Public Endpoint)" `
    -Method "POST" `
    -Endpoint "/api/vanigam/validate-epic" `
    -Headers @{} `
    -Data '{"epic_no":"TEST123"}' `
    -ExpectedStatus 200) {
    $pass_count++
} else {
    $fail_count++
}

# Test 10: Get Member
if (Test-Endpoint `
    -TestName "Get Member - Should Work (Public Endpoint)" `
    -Method "GET" `
    -Endpoint "/api/vanigam/member/TEST-ID" `
    -Headers @{} `
    -Data "" `
    -ExpectedStatus 200) {
    $pass_count++
} else {
    $fail_count++
}

# Test 11: Get QR Code
if (Test-Endpoint `
    -TestName "Get QR Code - Should Work (Public Endpoint)" `
    -Method "GET" `
    -Endpoint "/api/vanigam/qr/TEST-ID" `
    -Headers @{} `
    -Data "" `
    -ExpectedStatus 200) {
    $pass_count++
} else {
    $fail_count++
}

# Test 12: Verify PIN
if (Test-Endpoint `
    -TestName "Verify PIN - Should Work (Public Endpoint)" `
    -Method "POST" `
    -Endpoint "/api/vanigam/verify-pin" `
    -Headers @{} `
    -Data '{"unique_id":"TEST-ID","pin":"1234"}' `
    -ExpectedStatus 200) {
    $pass_count++
} else {
    $fail_count++
}

# Test 13: Check Loan Status
if (Test-Endpoint `
    -TestName "Check Loan Status - Should Work (Public Endpoint)" `
    -Method "POST" `
    -Endpoint "/api/vanigam/check-loan-status" `
    -Headers @{} `
    -Data '{"unique_id":"TEST-ID"}' `
    -ExpectedStatus 200) {
    $pass_count++
} else {
    $fail_count++
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "TEST SUMMARY" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "✓ Passed: $pass_count" -ForegroundColor Green
Write-Host "✗ Failed: $fail_count" -ForegroundColor Red
Write-Host "==========================================" -ForegroundColor Cyan

if ($fail_count -eq 0) {
    Write-Host "ALL TESTS PASSED!" -ForegroundColor Green
    exit 0
} else {
    Write-Host "SOME TESTS FAILED!" -ForegroundColor Red
    exit 1
}
