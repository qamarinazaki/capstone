<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerRating;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $table = 'ninjavan_data';
        
        // 1. Get filter status from request
        $selectedYear = $request->get('year', '2023');
        $selectedMonth = $request->get('month', 'all');

        // Create a base query
        $query = DB::table($table);

        // 2. Apply Year Filter 
        $query->where('Delivery_Date', 'LIKE', '%' . $selectedYear . '%');

        // 3. Apply Month Filter if a specific month is picked
        if ($selectedMonth !== 'all') {
            $formattedMonth = str_pad($selectedMonth, 2, '0', STR_PAD_LEFT);
            $query->where(function($q) use ($selectedMonth, $formattedMonth) {
                $q->where('Delivery_Date', 'LIKE', '%/' . $selectedMonth . '/%')
                  ->orWhere('Delivery_Date', 'LIKE', '%/' . $formattedMonth . '/%');
            });
        }

        // 4. Core Metrics Calculations
        $totalParcel = (clone $query)->count();
        $totalWeight = (clone $query)->sum('Original_Weight') ?: 0;
        $avgWeight   = (clone $query)->avg('Original_Weight') ?: 0;
        $delivered   = (clone $query)->where('Order_Granular_Status', 'LIKE', '%DELIVERED%')->count();

        // 5. STATES DISTRIBUTION (Normalizing State Names for Qamarina's Map)
        $stateStats = (clone $query)
            ->select('L1_Name as state', DB::raw('COUNT(*) as total'))
            ->groupBy('L1_Name')
            ->orderByDesc('total')
            ->get();
            
        // Map data to harmonize inconsistent database values with standard GeoJSON keys
        $normalizedLabels = [];
        $normalizedData = [];
        foreach ($stateStats as $row) {
            $name = strtoupper(trim($row->state));
            
            // Map common inconsistencies explicitly
            if ($name === 'PULAU PINANG') $name = 'PENANG';
            if ($name === 'KUALA LUMPUR') $name = 'W.P. KUALA LUMPUR';
            if ($name === 'LABUAN') $name = 'W.P. LABUAN';
            if ($name === 'PUTRAJAYA') $name = 'W.P. PUTRAJAYA';
            
            $normalizedLabels[] = $name;
            $normalizedData[] = $row->total;
        }
        $stateLabels = $normalizedLabels;
        $stateData = $normalizedData;

        // 6. Parcel Size Distribution
        $sizeStats = (clone $query)
            ->select('Parcel_Size_ID as size', DB::raw('COUNT(*) as total'))
            ->groupBy('Parcel_Size_ID')
            ->get();
        $sizeLabels = $sizeStats->pluck('size');
        $sizeData = $sizeStats->pluck('total');

        // 7. FIXED TREND LOGIC (Always tracks the entire year context for full lines)
        $trendQuery = DB::table($table)->where('Delivery_Date', 'LIKE', '%' . $selectedYear . '%');
        
        $trend = $trendQuery->select(
                DB::raw("DATE_FORMAT(Delivery_Date, '%M') as month_name"),
                DB::raw("DATE_FORMAT(Delivery_Date, '%m') as month_num"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(
                DB::raw("DATE_FORMAT(Delivery_Date, '%m')"),
                DB::raw("DATE_FORMAT(Delivery_Date, '%M')")
            )
            ->orderBy('month_num', 'asc') 
            ->get();
            
        $trendLabels = $trend->pluck('month_name'); 
        $trendData = $trend->pluck('total');

        // 8. Gender Distribution
        $genderData = (clone $query)
            ->select('Gender', DB::raw('count(*) as count'))
            ->groupBy('Gender')
            ->get();

        return view('dashboard', compact(
            'totalParcel', 'totalWeight', 'avgWeight', 'delivered', 
            'stateLabels', 'stateData', 'sizeLabels', 'sizeData',
            'trendLabels', 'trendData', 'genderData', 'selectedMonth', 'selectedYear'
        ));
    }

    /**
     * Feedback Page Logic (Customer Page)
     */
    public function feedback()
    {
        $table = 'feedback_data';

        // 1. Get all raw feedback rows
        $feedback = DB::table($table)->orderByDesc('id')->get();

        // 2. Fetch survey responses calculations
        $avgPunctuality = CustomerRating::avg('rating_punctuality') ?? 0;
        $avgCondition   = CustomerRating::avg('rating_condition') ?? 0;
        $avgAttitude    = CustomerRating::avg('rating_attitude') ?? 0;
        $avgTrust       = CustomerRating::avg('rating_trust') ?? 0;
        $totalResponses = CustomerRating::count();

        // 3. Trust Distribution for Chart
        $trustStats = DB::table($table)
            ->select('trust_rating', DB::raw('count(*) as total'))
            ->groupBy('trust_rating')
            ->orderBy('trust_rating')
            ->get();
        
        $trustLabels = $trustStats->pluck('trust_rating');
        $trustData = $trustStats->pluck('total');

        return view('feedback', compact(
            'feedback', 
            'avgPunctuality', 
            'avgCondition', 
            'avgAttitude', 
            'avgTrust',
            'totalResponses',
            'trustLabels', 
            'trustData'
        ));
    }
}