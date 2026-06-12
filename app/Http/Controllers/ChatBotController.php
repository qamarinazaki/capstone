<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use Exception;

class ChatBotController extends Controller
{
    public function index() 
    { 
        return view('chatbot'); 
    }

    public function message(Request $request)
    {
        $rawMessage = $request->input('message');
        if (empty($rawMessage)) {
            return response()->json(['reply' => "I'm listening! Please type an operational query."]);
        }

        // Clean text markers & normalize casing
        $message = str_replace(['"', "'", '“', '”', '‘', '’'], '', $rawMessage);
        $message = strtolower(trim($message));

        // 1. ADVANCED LOGISTICS DATE PARSING LAYER (Captures years 2023 up to 2029)
        $year = null;
        if (preg_match('/\b(202[3-9])\b/', $message, $matches)) {
            $year = $matches[1];
        }

        $month = null;
        $altMonth = null; 
        $monthName = null;
        
        $monthMap = [
            'jan' => ['01', '1'], 'january' => ['01', '1'],
            'feb' => ['02', '2'], 'february' => ['02', '2'],
            'mar' => ['03', '3'], 'march' => ['03', '3'],
            'apr' => ['04', '4'], 'april' => ['04', '4'],
            'may' => ['05', '5'],
            'jun' => ['06', '6'], 'june' => ['06', '6'],
            'jul' => ['07', '7'], 'july' => ['07', '7'],
            'aug' => ['08', '8'], 'august' => ['08', '8'],
            'sep' => ['09', '9'], 'september' => ['09', '9'],
            'oct' => ['10', '10'], 'october' => ['10', '10'],
            'nov' => ['11', '11'], 'november' => ['11', '11'],
            'dec' => ['12', '12'], 'december' => ['12', '12']
        ];

        foreach ($monthMap as $word => $digits) {
            if (str_contains($message, $word)) {
                $month = $digits[0];
                $altMonth = $digits[1];
                $monthName = ucfirst($word);
                break;
            }
        }

        // 2. MALAYSIAN GEOGRAPHIC STATE DETECTION ENGINE
        $states = [
            'johor', 'kedah', 'kelantan', 'melaka', 'negeri sembilan', 
            'pahang', 'perak', 'perlis', 'pulau pinang', 'penang', 
            'sabah', 'sarawak', 'selangor', 'terengganu', 'kuala lumpur'
        ];
        $detectedState = null;
        foreach ($states as $state) {
            if (str_contains($message, $state)) {
                $detectedState = $state;
                break;
            }
        }

        // 3. LOGISTICS KEYWORD MATCH SCORE DICTIONARY
        $cleanTokenString = str_replace(['?', '!', '.', ',', '/', '-'], ' ', $message);
        $tokens = explode(' ', $cleanTokenString);
        $tokens = array_filter(array_map('trim', $tokens)); 
        
        $parcelKeywords   = ['parcel', 'parcels', 'order', 'orders', 'count', 'volume', 'total', 'many', 'much', 'shipment', 'shipments', 'quantity', 'amount', 'totals'];
        $weightKeywords   = ['weight', 'weights', 'heavy', 'kg', 'kilogram', 'kilograms', 'mass', 'avg', 'average', 'load'];
        $deliveryKeywords = ['delivered', 'delivery', 'deliveries', 'success', 'done', 'completed', 'status', 'fulfilled'];
        $feedbackKeywords = ['rating', 'ratings', 'score', 'scores', 'customer', 'feedback', 'satisfaction', 'review', 'reviews', 'punctuality', 'attitude', 'condition', 'survey', 'respondents', 'stars'];

        $parcelScore   = count(array_intersect($tokens, $parcelKeywords));
        $weightScore   = count(array_intersect($tokens, $weightKeywords));
        $deliveryScore = count(array_intersect($tokens, $deliveryKeywords));
        $feedbackScore = count(array_intersect($tokens, $feedbackKeywords));

        $totalDatabaseScore = $parcelScore + $weightScore + $deliveryScore + $feedbackScore;

        // =========================================================================
        // 📊 ROUTE A: RUN DATABASE LOOKUPS (DYNAMICAL TO CHOSEN PARAMETERS)
        // =========================================================================
        if ($totalDatabaseScore > 0) {
            try {
                $dbColumns = [
                    'state'          => 'L1_Name',              
                    'delivery_date'  => 'Delivery_Date',        
                    'weight'         => 'Original_Weight',      
                    'status'         => 'Order_Granular_Status' 
                ];

                // --- SECTION A: PARCEL COUNT DATA ---
                if ($parcelScore > 0 && $parcelScore >= max($weightScore, $deliveryScore, $feedbackScore)) {
                    if (Schema::hasTable('ninjavan_data')) {
                        $query = DB::table('ninjavan_data');
                        
                        if ($year) {
                            if ($month) {
                                $query->where(function($q) use ($month, $altMonth, $year, $dbColumns) {
                                    $q->where($dbColumns['delivery_date'], 'LIKE', '%/' . $month . '/' . $year)
                                      ->orWhere($dbColumns['delivery_date'], 'LIKE', '%/' . $altMonth . '/' . $year)
                                      ->orWhere($dbColumns['delivery_date'], 'LIKE', $year . '-' . $month . '-%');
                                });
                            } else {
                                $query->where(function($q) use ($year, $dbColumns) {
                                    $q->where($dbColumns['delivery_date'], 'LIKE', '%/' . $year)
                                      ->orWhere($dbColumns['delivery_date'], 'LIKE', $year . '-%');
                                });
                            }
                        }
                        if ($detectedState) {
                            $query->where($dbColumns['state'], 'LIKE', '%' . $detectedState . '%');
                        }
                        
                        $count = $query->count();

                        // Dynamic evaluation block: If rows exist, return data. If 0 records exist for a future year (e.g. 2026), fall through to Gemini API.
                        if ($count > 0 || $year === '2023' || $year === '2024' || $year === '2025') {
                            $contextStr = ($monthName ? $monthName . ' ' : '') . ($year ? $year : 'All Historical Records');
                            $stateStr = $detectedState ? " within **" . ucwords($detectedState) . "**" : "";
                            
                            return response()->json([
                                'reply' => "### Logistics Volume Inventory Report\nOur system records indicate a total metric volume of **" . number_format($count) . " parcels** logged for **" . $contextStr . "**" . $stateStr . "."
                            ]);
                        }
                    }
                }

                // --- SECTION B: PACKAGING CARGO WEIGHT DATA ---
                if ($weightScore > 0 && $weightScore >= max($parcelScore, $deliveryScore, $feedbackScore)) {
                    if (Schema::hasTable('ninjavan_data')) {
                        $query = DB::table('ninjavan_data');
                        
                        if ($year) {
                            if ($month) {
                                $query->where(function($q) use ($month, $altMonth, $year, $dbColumns) {
                                    $q->where($dbColumns['delivery_date'], 'LIKE', '%/' . $month . '/' . $year)
                                      ->orWhere($dbColumns['delivery_date'], 'LIKE', '%/' . $altMonth . '/' . $year)
                                      ->orWhere($dbColumns['delivery_date'], 'LIKE', $year . '-' . $month . '-%');
                                });
                            } else {
                                $query->where(function($q) use ($year, $dbColumns) {
                                    $q->where($dbColumns['delivery_date'], 'LIKE', '%/' . $year)
                                      ->orWhere($dbColumns['delivery_date'], 'LIKE', $year . '-%');
                                });
                            }
                        }
                        if ($detectedState) {
                            $query->where($dbColumns['state'], 'LIKE', '%' . $detectedState . '%');
                        }
                        
                        $avg = $query->avg($dbColumns['weight']) ?? 0;
                        $countCheck = $query->count();

                        if ($countCheck > 0 || $year === '2023' || $year === '2024' || $year === '2025') {
                            $contextStr = ($monthName ? $monthName . ' ' : '') . ($year ? $year : 'All Historical Records');
                            $stateStr = $detectedState ? " within **" . ucwords($detectedState) . "**" : "";
                            
                            return response()->json([
                                'reply' => "### Physical Logistics Cargo Report\nDuring **" . $contextStr . "**" . $stateStr . ", the calculated mean average gross freight mass across items was **" . number_format($avg, 2) . " kg** per package unit."
                            ]);
                        }
                    }
                }

                // --- SECTION C: FULFILLMENT STATUS DELIVERY DATA ---
                if ($deliveryScore > 0 && $deliveryScore >= max($parcelScore, $weightScore, $feedbackScore)) {
                    if (Schema::hasTable('ninjavan_data')) {
                        $query = DB::table('ninjavan_data')->where($dbColumns['status'], 'LIKE', '%DELIVERED%');
                        
                        if ($year) {
                            if ($month) {
                                $query->where(function($q) use ($month, $altMonth, $year, $dbColumns) {
                                    $q->where($dbColumns['delivery_date'], 'LIKE', '%/' . $month . '/' . $year)
                                      ->orWhere($dbColumns['delivery_date'], 'LIKE', '%/' . $altMonth . '/' . $year)
                                      ->orWhere($dbColumns['delivery_date'], 'LIKE', $year . '-' . $month . '-%');
                                });
                            } else {
                                $query->where(function($q) use ($year, $dbColumns) {
                                    $q->where($dbColumns['delivery_date'], 'LIKE', '%/' . $year)
                                      ->orWhere($dbColumns['delivery_date'], 'LIKE', $year . '-%');
                                });
                            }
                        }
                        if ($detectedState) {
                            $query->where($dbColumns['state'], 'LIKE', '%' . $detectedState . '%');
                        }
                        
                        $count = $query->count();

                        if ($count > 0 || $year === '2023' || $year === '2024' || $year === '2025') {
                            $contextStr = ($monthName ? $monthName . ' ' : '') . ($year ? $year : 'All Historical Records');
                            $stateStr = $detectedState ? " within **" . ucwords($detectedState) . "**" : "";
                            
                            return response()->json([
                                'reply' => "### Operational Fulfillment Metrics Summary\nFulfillment tracing verifies **" . number_format($count) . " successful deliveries** successfully processed for **" . $contextStr . "**" . $stateStr . "."
                            ]);
                        }
                    }
                }

                // --- SECTION D: CUSTOMER SATISFACTION SURVEY RATINGS ---
                if ($feedbackScore > 0) {
                    if (Schema::hasTable('customer_ratings')) {
                        $count = DB::table('customer_ratings')->count();
                        if ($count > 0) {
                            $avgPunctuality = DB::table('customer_ratings')->avg('rating_punctuality') ?? 0;
                            $avgCondition   = DB::table('customer_ratings')->avg('rating_condition') ?? 0;
                            $avgAttitude    = DB::table('customer_ratings')->avg('rating_attitude') ?? 0;

                            return response()->json([
                                'reply' => "### Customer Satisfaction Index (CSI) Summary Report\nAggregated metrics compiled across **" . number_format($count) . " submitted client reviews**:\n\n* **Transit Speed / Punctuality Rating:** " . number_format($avgPunctuality, 1) . " / 5.0 ★\n* **Parcel Condition / Quality Control Handling:** " . number_format($avgCondition, 1) . " / 5.0 ★\n* **Courier Professionalism / Rider Conduct:** " . number_format($avgAttitude, 1) . " / 5.0 ★"
                            ]);
                        }
                    }
                }

            } catch (Exception $e) {
                logger()->error('Local Database Error: ' . $e->getMessage());
            }
        }

        // =========================================================================
        // 🌐 ROUTE B: GENERAL CONVERSATION & INTELLIGENT AI SYSTEM ASSISTANCE
        // =========================================================================
        $apiKey = env('GEMINI_API_KEY');

        if (!empty($apiKey)) {
            try {
                $cleanKey = trim(str_replace(['"', "'"], '', $apiKey));

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json'
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $cleanKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => "You are NinjaVault Core Intelligence, a helpful analytics dashboard assistant for our internal NinjaVan tracking system. Write a brief, professional response answering this user query: " . $rawMessage . ". Current Year contextual boundary: 2026. If the user asks about operational predictions or generic layout planning regarding years like 2026 or above, assist them with smart professional analytics speculation."]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $aiData = $response->json();
                    $aiReply = $aiData['candidates'][0]['content']['parts'][0]['text'] ?? null;

                    if (!empty($aiReply)) {
                        return response()->json(['reply' => $aiReply]);
                    }
                } else {
                    logger()->error('Gemini API Error Payload: ' . $response->body());
                }
            } catch (Exception $e) {
                logger()->error('Gemini API Network Exception: ' . $e->getMessage());
            }
        }

        // =========================================================================
        // 🛑 ROUTE C: CRITICAL FALLBACK SYSTEM (Runs if API key is blank/failed)
        // =========================================================================
        return response()->json([
            'reply' => "### NinjaVault Core Intelligence\n" .
                       "I was unable to process your request through our AI core service. If you are tracking database matrices, please formulate queries using parameters like:\n\n" .
                       "* *\"What is our total parcel count in Terengganu for 2025?\"*\n" .
                       "* *\"Show customer survey satisfaction score summary\"*"
        ]);
    }
}