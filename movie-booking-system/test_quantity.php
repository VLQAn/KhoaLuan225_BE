<?php

require_once 'vendor/autoload.php';

// Test regex patterns directly
function testQuantityExtraction($message) {
    $lowerMessage = mb_strtolower($message);
    
    echo "Testing: '$message'\n";
    echo "Lowercase: '$lowerMessage'\n";
    
    // Pattern 1
    if (preg_match('/(\d+)\s*(?:cai|chiếc|tấm|ve|vé|ticket|vé phim|vé xem|vé chiếu|bộ vé)/u', $lowerMessage, $matches)) {
        echo "Pattern 1 MATCHED: " . $matches[1] . "\n";
        return (int) $matches[1];
    }
    echo "Pattern 1 NO MATCH\n";
    
    // Pattern 2
    if (preg_match('/(đặt|mua|book|order|buy)?\s*(\d+)\s*(?:cai|chiếc|tấm|ve|vé|ticket)/u', $lowerMessage, $matches)) {
        echo "Pattern 2 MATCHED: " . $matches[2] . "\n";
        return (int) $matches[2];
    }
    echo "Pattern 2 NO MATCH\n";
    
    return null;
}

// Test messages
$messages = [
    "Đặt 2 vé star wars",
    "đặt 2 vé star wars",
    "mua 3 vé Avengers",
    "tôi muốn 5 vé phim Avatar",
    "suất 1 (yêu cầu 2 vé)"
];

foreach ($messages as $msg) {
    $result = testQuantityExtraction($msg);
    echo "Result: " . ($result ?? "null") . "\n";
    echo "---\n";
}
