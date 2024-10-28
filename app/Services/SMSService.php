<?php 
namespace App\Services;

class SMSService {
    public static function bookingSMS() {
        $message = "SHABANI RAJABU\n";
        $message .= "T 111 ABC\n";
        $message .= "From: Arusha\n";
        $message .= "To: Moshi\n";
        $message .= "Seat No: A2\n";
        $message .= "Ticket No: BM00023\n";
        $message .= "Date: 23.10.2024\n";
        $message .= "Time: 18:00\n";
        $message .= "Amount: 50,000\n\n";
        $message .= "AGENT INFO\n";
        $message .= "FADHILA MANGULA (BM0002)\n";
        $message .= "0699183267\n\n";
        $message .= "Thank you.";
    }
}