<?php

use App\Models\Booking;
use Illuminate\Http\Request;

if (!function_exists('agentTimetableCollection')) {
    function agentTimetableCollection($timetableId, $agentId) {
        return Booking::whereTimetableId($timetableId)
        ->whereAgentId($agentId)->sum('fare');
    }
}

if (!function_exists('agentTimetableTickets')) {
    function agentTimetableTickets($timetableId, $agentId) {
        return Booking::whereTimetableId($timetableId)
        ->whereAgentId($agentId)->get()->count();
    }
}