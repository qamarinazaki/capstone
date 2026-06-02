<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // 4. Metrics
        $totalParcel = (clone $query)->count();
        $totalWeight = (clone $query)->sum('Original_Weight') ?: 0;
        $avgWeight   = (clone $query)->avg('Original_Weight') ?: 0;
        $delivered   = (clone $query)->where('Order_Granular_Status', 'LIKE', '%DELIVERED%')->count();

        // 5. TOP 3 STATES
        $stateStats = (clone $query)
            ->select('L1_Name as state', DB::raw('COUNT(*) as total'))
            ->groupBy('L1_Name')
            ->orderByDesc('total')
            //->limit(3)
            ->get();
        $stateLabels = $stateStats->pluck('state');
        $stateData = $stateStats->pluck('total');

        // 6. Parcel Size Distribution
        $sizeStats = (clone $query)
            ->select('Parcel_Size_ID as size', DB::raw('COUNT(*) as total'))
            ->groupBy('Parcel_Size_ID')
            ->get();
        $sizeLabels = $sizeStats->pluck('size');
        $sizeData = $sizeStats->pluck('total');

        // 7. Trend Logic (Untuk format YYYY-MM-DD)
$trend = (clone $query)
    ->select(
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
     * Feedback Page Logic
     */
    public function feedback()
    {
        $table = 'feedback_data';

        // 1. Get all raw feedback for the comments table
        $feedback = DB::table($table)->orderByDesc('id')->get();

        // 2. Calculate Average Ratings (1-5 scale)
        $avgPunctuality = DB::table($table)->avg('punctuality') ?: 0;
        $avgCondition   = DB::table($table)->avg('condition_rating') ?: 0;
        $avgAttitude    = DB::table($table)->avg('attitude') ?: 0;

        // 3. Trust Distribution for Chart (Question 6)
        $trustStats = DB::table($table)
            ->select('trust_rating', DB::raw('count(*) as total'))
            ->groupBy('trust_rating')
            ->orderBy('trust_rating')
            ->get();
        
        $trustLabels = $trustStats->pluck('trust_rating'); // e.g. 1, 2, 3, 4, 5
        $trustData = $trustStats->pluck('total');

        return view('feedback', compact(
            'feedback', 
            'avgPunctuality', 
            'avgCondition', 
            'avgAttitude', 
            'trustLabels', 
            'trustData'
        ));
    }
}