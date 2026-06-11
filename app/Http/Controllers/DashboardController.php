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
        
        // ======================================================================
        // 1. Get Filter Status & Sanitize Year Input (Removes "Operations" Text)
        // ======================================================================
        $rawYear = $request->get('year', '2023');

        if (preg_match('/(202\d)/', $rawYear, $matches)) {
            $selectedYear = $matches[1];
        } else {
            $selectedYear = '2023'; 
        }

        $selectedMonth = $request->get('month', 'all');
        $query = DB::table($table);

        // ======================================================================
        // 2. HYBRID YEAR FILTER (Matches both '2024-05-12' and '12/05/2024')
        // ======================================================================
        $query->where(function($q) use ($selectedYear) {
            $q->where('Delivery_Date', 'LIKE', '%' . $selectedYear . '%')
              ->orWhere('Create_Time', 'LIKE', '%' . $selectedYear . '%');
        });

        // ======================================================================
        // 3. HYBRID MONTH FILTER (Handles '01', '1', '/1/', and '-01-')
        // ======================================================================
        if ($selectedMonth !== 'all') {
            $singleMonth = (int)$selectedMonth; 
            $paddedMonth = str_pad($selectedMonth, 2, '0', STR_PAD_LEFT); 
            
            $query->where(function($q) use ($singleMonth, $paddedMonth, $selectedYear) {
                // Slash text-string format matching (e.g., /1/2023 or /01/2023)
                $q->where('Delivery_Date', 'LIKE', '%/' . $singleMonth . '/' . $selectedYear . '%')
                  ->orWhere('Delivery_Date', 'LIKE', '%/' . $paddedMonth . '/' . $selectedYear . '%')
                  // Hyphen native database format matching (e.g., 2024-01-)
                  ->orWhere('Delivery_Date', 'LIKE', $selectedYear . '-' . $paddedMonth . '%')
                  ->orWhere('Create_Time', 'LIKE', $selectedYear . '-' . $paddedMonth . '%');
            });
        }

        // ======================================================================
        // 4. Core Metrics Calculations
        // ======================================================================
        $totalParcel = (clone $query)->count();
        $totalWeight = (clone $query)->sum('Original_Weight') ?: 0;
        $avgWeight   = (clone $query)->avg('Original_Weight') ?: 0;
        $delivered   = (clone $query)->where('Order_Granular_Status', 'LIKE', '%DELIVERED%')->count();

        // ======================================================================
        // 5. Regional States Distribution
        // ======================================================================
        $stateStats = (clone $query)
            ->select('L1_Name as state', DB::raw('COUNT(*) as total'))
            ->groupBy('L1_Name')
            ->orderByDesc('total')
            ->get();
            
        $normalizedLabels = [];
        $normalizedData = [];
        foreach ($stateStats as $row) {
            if (!empty($row->state)) {
                $name = strtoupper(trim($row->state));
                if ($name === 'PULAU PINANG') $name = 'PENANG';
                if ($name === 'KUALA LUMPUR') $name = 'W.P. KUALA LUMPUR';
                if ($name === 'LABUAN')       $name = 'W.P. LABUAN';
                if ($name === 'PUTRAJAYA')    $name = 'W.P. PUTRAJAYA';
                
                $normalizedLabels[] = $name;
                $normalizedData[] = $row->total;
            }
        }
        $stateLabels = $normalizedLabels;
        $stateData = $normalizedData;

        // ======================================================================
        // 6. Parcel Size Distribution
        // ======================================================================
        $sizeStats = (clone $query)
            ->select('Parcel_Size_ID as size', DB::raw('COUNT(*) as total'))
            ->groupBy('Parcel_Size_ID')
            ->get();
        $sizeLabels = $sizeStats->pluck('size');
        $sizeData = $sizeStats->pluck('total');

        // ======================================================================
        // 7. FIXED HYBRID TREND LINE ENGINE (Extracts month from slashes AND hyphens)
        // ======================================================================
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $trend = DB::table($table)
            ->where(function($q) use ($selectedYear) {
                $q->where('Delivery_Date', 'LIKE', '%' . $selectedYear . '%')
                  ->orWhere('Create_Time', 'LIKE', '%' . $selectedYear . '%');
            })
            ->select(
                DB::raw("CASE 
                    WHEN Delivery_Date LIKE '%/%' THEN LPAD(TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(Delivery_Date, '/', 2), '/', -1)), 2, '0')
                    WHEN Delivery_Date LIKE '%-%' THEN SUBSTRING(Delivery_Date, 6, 2)
                    WHEN Create_Time LIKE '%-%' THEN SUBSTRING(Create_Time, 6, 2)
                    ELSE '01'
                END as month_num"),
                DB::raw("COUNT(*) as total")
            )
            ->groupBy(DB::raw("CASE 
                    WHEN Delivery_Date LIKE '%/%' THEN LPAD(TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(Delivery_Date, '/', 2), '/', -1)), 2, '0')
                    WHEN Delivery_Date LIKE '%-%' THEN SUBSTRING(Delivery_Date, 6, 2)
                    WHEN Create_Time LIKE '%-%' THEN SUBSTRING(Create_Time, 6, 2)
                    ELSE '01'
                END"))
            ->orderBy('month_num', 'asc') 
            ->get();

        $monthMap = [
            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];

        $trendLabels = [];
        $trendData = [];
        foreach ($trend as $row) {
            $cleanNum = str_pad(trim($row->month_num), 2, '0', STR_PAD_LEFT);
            if (array_key_exists($cleanNum, $monthMap)) {
                $trendLabels[] = $monthMap[$cleanNum];
                $trendData[] = $row->total;
            }
        }

        // ======================================================================
        // 8. CASE-INSENSITIVE GENDER DISTRIBUTION
        // ======================================================================
        $genderRaw = (clone $query)
            ->select('Gender', DB::raw('COUNT(*) as count'))
            ->whereNotNull('Gender')
            ->where('Gender', '!=', '')
            ->groupBy('Gender')
            ->get();

        $genderMap = [];
        foreach ($genderRaw as $row) {
            $gName = ucfirst(strtolower(trim($row->Gender)));
            if ($gName === 'M') $gName = 'Male';
            if ($gName === 'F') $gName = 'Female';
            
            $genderMap[$gName] = ($genderMap[$gName] ?? 0) + $row->count;
        }

        $genderData = [];
        foreach ($genderMap as $key => $val) {
            $genderData[] = (object) ['Gender' => $key, 'count' => $val];
        }

        return view('dashboard', compact(
            'totalParcel', 'totalWeight', 'avgWeight', 'delivered', 
            'stateLabels', 'stateData', 'sizeLabels', 'sizeData',
            'trendLabels', 'trendData', 'genderData', 'selectedMonth', 'selectedYear'
        ));
    }

    public function feedback()
    {
        $table = 'feedback_data';
        $feedback = DB::table($table)->orderByDesc('id')->get();

        $avgPunctuality = CustomerRating::avg('rating_punctuality') ?? 0;
        $avgCondition   = CustomerRating::avg('rating_condition') ?? 0;
        $avgAttitude    = CustomerRating::avg('rating_attitude') ?? 0;
        $avgTrust       = CustomerRating::avg('rating_trust') ?? 0;
        $totalResponses = CustomerRating::count();

        $trustStats = DB::table($table)
            ->select('trust_rating', DB::raw('count(*) as total'))
            ->groupBy('trust_rating')
            ->orderBy('trust_rating')
            ->get();
        
        $trustLabels = $trustStats->pluck('trust_rating');
        $trustData = $trustStats->pluck('total');

        return view('feedback', compact(
            'feedback', 'avgPunctuality', 'avgCondition', 'avgAttitude', 'avgTrust',
            'totalResponses', 'trustLabels', 'trustData'
        ));
    }

    public function flash(Request $request)
    {
        $table = 'ninjavan_data';
        $selectedDate = $request->get('date');
        
        if (!$selectedDate) {
            $latestRow = DB::table($table)->orderBy('id', 'desc')->first();
            $selectedDate = $latestRow ? $latestRow->Delivery_Date : '2023-03-13';
        }

        $cleanedDate = trim($selectedDate);

        $fetchRowsClosure = function($targetDate) use ($table) {
            $searchDate1 = $targetDate; 
            $searchDate2 = $targetDate;
            $isoDate = $targetDate;

            if (strpos($targetDate, '-') !== false && strlen($targetDate) === 10) {
                try {
                    $time = strtotime($targetDate);
                    $searchDate1 = date('d/m/Y', $time); 
                    $searchDate2 = date('j/n/Y', $time); 
                } catch (\Exception $e) {}
            } else if (strpos($targetDate, '/') !== false) {
                try {
                    $time = strtotime(str_replace('/', '-', $targetDate));
                    $isoDate = date('Y-m-d', $time);
                    $searchDate1 = date('d/m/Y', $time);
                    $searchDate2 = date('j/n/Y', $time);
                } catch (\Exception $e) {}
            }

            return DB::table($table)
                ->where(function($q) use ($targetDate, $searchDate1, $searchDate2, $isoDate) {
                    $q->where('Delivery_Date', 'LIKE', $targetDate . '%')
                      ->orWhere('Delivery_Date', 'LIKE', $searchDate1 . '%')
                      ->orWhere('Delivery_Date', 'LIKE', $searchDate2 . '%')
                      ->orWhere('Delivery_Date', 'LIKE', $isoDate . '%');
                    
                    $hyphen1 = str_replace('/', '-', $searchDate1);
                    $hyphen2 = str_replace('/', '-', $searchDate2);
                    $q->orWhere('Delivery_Date', 'LIKE', $hyphen1 . '%')
                      ->orWhere('Delivery_Date', 'LIKE', $hyphen2 . '%');
                })
                ->get();
        };

        $flashRows = $fetchRowsClosure($cleanedDate);

        if ($flashRows->count() === 0) {
            try {
                $searchYear = '2023';
                if (strpos($cleanedDate, '-') !== false) {
                    $searchYear = explode('-', $cleanedDate)[0];
                } elseif (strpos($cleanedDate, '/') !== false) {
                    $parts = explode('/', $cleanedDate);
                    $searchYear = (count($parts) === 3) ? $parts[2] : '2023';
                }

                $fallbackRow = DB::table($table)
                    ->where('Delivery_Date', 'LIKE', '%' . $searchYear . '%')
                    ->whereNotNull('Delivery_Date')
                    ->first();
                    
                if ($fallbackRow) {
                    $rawDate = trim($fallbackRow->Delivery_Date);
                    if (preg_match('/^(\d+[\/\-]\d+[\/\-]\d+)/', $rawDate, $matches)) {
                        $selectedDate = $matches[1];
                    } else {
                        $selectedDate = substr($rawDate, 0, 10);
                    }
                    $cleanedDate = trim($selectedDate);
                    $flashRows = $fetchRowsClosure($cleanedDate);
                }
            } catch (\Exception $e) {}
        }

        $totalParcels = $flashRows->count();
        $totalWeight = $flashRows->sum(function($row) { return (float) ($row->Original_Weight ?? 0); });
        $avgWeight = $totalParcels > 0 ? ($totalWeight / $totalParcels) : 0;

        $stateBreakdown = [];
        foreach ($flashRows as $row) {
            if (!empty($row->L1_Name)) {
                $cleanState = strtoupper(trim($row->L1_Name));
                if ($cleanState === 'PULAU PINANG') $cleanState = 'PENANG';
                if ($cleanState === 'KUALA LUMPUR') $cleanState = 'W.P. KUALA LUMPUR';
                if ($cleanState === 'LABUAN')       $cleanState = 'W.P. LABUAN';
                if ($cleanState === 'PUTRAJAYA')    $cleanState = 'W.P. PUTRAJAYA';
                
                $stateBreakdown[$cleanState] = ($stateBreakdown[$cleanState] ?? 0) + 1;
            }
        }
        arsort($stateBreakdown);

        $sizes = ['Small' => 0, 'Other' => 0];
        foreach ($flashRows as $row) {
            if (isset($row->Parcel_Size_ID) && $row->Parcel_Size_ID == 1) {
                $sizes['Small']++;
            } else {
                $sizes['Other']++;
            }
        }

        return view('flash', compact('selectedDate', 'totalParcels', 'totalWeight', 'avgWeight', 'stateBreakdown', 'sizes'));
    }
}