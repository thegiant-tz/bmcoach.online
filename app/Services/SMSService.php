<?php 
namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class SMSService {
    public static function bookingSMS(Booking $booking, $ticketNo) {
        $message = $booking->psg_name."\r\n";
        $message .= $booking->bus->number. "\r\n";
        $message .= "From: ".$booking->route->from."\r\n";
        $message .= "To: ".$booking->route->to."\r\n";
        $message .= "Seat No: ".$booking->seat_no."\r\n";
        $message .= "Ticket No: $ticketNo\r\n";
        $message .= "Date: ".Carbon::parse($booking->dep_date)->format('d.m.Y')."\r\n";
        $message .= "Time: ".Carbon::parse($booking->dep_date)->format('H:i')."\r\n";
        $message .= "Amount: ".number_format($booking->fare)."\r\n\r\n";
        $message .= "AGENT INFO\r\n";
        $message .= $booking->agent->name . " (".$booking->agent->username.")\r\n";
        $message .= $booking->agent->phone."\r\n\r\n";
        $message .= "Thank you.";
        return $message;
    }
}