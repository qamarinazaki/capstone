<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotController extends Controller
{
    public function index() 
    { 
        return view('chatbot'); 
    }

    public function message(Request $request)
    {
        // Get raw message input
        $rawMessage = $request->input('message');
        if (empty($rawMessage)) {
            return response()->json(['reply' => "I'm listening! Please type an operational query."]);
        }

        // SANITIZATION LAYER: Strip out quotation marks and trim spaces
        $message = str_replace(['"', "'", '“', '”', '‘', '’'], '', $rawMessage);
        $message = strtolower(trim($message));

        // 1. ADVANCED DATE PARSING LAYER (DD/MM/YYYY STRING EXTRACTOR)
        $year = null;
        if (preg_match('/\b(202[2-6])\b/', $message, $matches)) {
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

        // 2. MALAYSIAN GEOGRAPHIC STATE DETECTION
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

        // 3. TOKEN DICTIONARIES FOR BALANCED ROUTING
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

        // 4. LOCAL DATABASE EXECUTION LAYER ROUTING

        // --- PARCEL VOLUME MATCH ---
        if ($parcelScore > 0 && $parcelScore >= max($weightScore, $deliveryScore, $feedbackScore)) {
            $query = DB::table('ninjavan_data');
            if ($year) {
                if ($month) {
                    $query->where(function($q) use ($month, $altMonth, $year) {
                        $q->where('Delivery_Date', 'LIKE', '%/' . $month . '/' . $year)
                          ->orWhere('Delivery_Date', 'LIKE', '%/' . $altMonth . '/' . $year);
                    });
                } else {
                    $query->where('Delivery_Date', 'LIKE', '%/' . $year);
                }
            }
            if ($detectedState) {
                $query->where('Recipient_State', 'LIKE', '%' . $detectedState . '%');
            }
            $count = $query->count();
            if ($count > 0) {
                $contextStr = ($monthName ? $monthName . ' ' : '') . ($year ? $year : 'Historical Timeline');
                $stateStr = $detectedState ? " within **" . ucfirst($detectedState) . "**" : "";
                return response()->json(['reply' => "Our shipping metrics indicate **" . number_format($count) . " parcels** were logged for **" . $contextStr . "**" . $stateStr . "."]);
            }
        }

        // --- WEIGHT MATCH ---
        if ($weightScore > 0 && $weightScore >= max($parcelScore, $deliveryScore, $feedbackScore)) {
            $query = DB::table('ninjavan_data');
            if ($year) {
                if ($month) {
                    $query->where(function($q) use ($month, $altMonth, $year) {
                        $q->where('Delivery_Date', 'LIKE', '%/' . $month . '/' . $year)
                          ->orWhere('Delivery_Date', 'LIKE', '%/' . $altMonth . '/' . $year);
                    });
                } else {
                    $query->where('Delivery_Date', 'LIKE', '%/' . $year);
                }
            }
            $avg = $query->avg('Original_Weight') ?? 0;
            if ($avg > 0) {
                $contextStr = ($monthName ? $monthName . ' ' : '') . ($year ? $year : 'Historical Timeline');
                return response()->json(['reply' => "During **" . $contextStr . "**, the calculated average freight cargo mass was **" . number_format($avg, 2) . " kg** per transit package."]);
            }
        }

        // --- DELIVERY STATUS MATCH ---
        if ($deliveryScore > 0 && $deliveryScore >= max($parcelScore, $weightScore, $feedbackScore)) {
            $query = DB::table('ninjavan_data')->where('Order_Granular_Status', 'LIKE', '%DELIVERED%');
            if ($year) {
                if ($month) {
                    $query->where(function($q) use ($month, $altMonth, $year) {
                        $q->where('Delivery_Date', 'LIKE', '%/' . $month . '/' . $year)
                          ->orWhere('Delivery_Date', 'LIKE', '%/' . $altMonth . '/' . $year);
                    });
                } else {
                    $query->where('Delivery_Date', 'LIKE', '%/' . $year);
                }
            }
            $count = $query->count();
            if ($count > 0) {
                $contextStr = ($monthName ? $monthName . ' ' : '') . ($year ? $year : 'Historical Timeline');
                return response()->json(['reply' => "Fulfillment metrics confirm **" . number_format($count) . " successful deliveries** recorded across **" . $contextStr . "**. "]);
            }
        }

        // --- CUSTOMER RATING MATCH ---
        if ($feedbackScore > 0) {
            $count = DB::table('customer_ratings')->count();
            if ($count > 0) {
                $avgPunctuality = DB::table('customer_ratings')->avg('rating_punctuality') ?? 0;
                $avgCondition   = DB::table('customer_ratings')->avg('rating_condition') ?? 0;
                $avgAttitude    = DB::table('customer_ratings')->avg('rating_attitude') ?? 0;

                return response()->json([
                    'reply' => "### Customer Evaluation & Satisfaction Summary Report\n" .
                               "Calculated across **" . number_format($count) . " active survey respondents**:\n\n" .
                               "* **Courier Service Speed / Punctuality Rating:** " . number_format($avgPunctuality, 1) . " / 5.0 ★\n" .
                               "* **Package Condition / Quality Assurance:** " . number_format($avgCondition, 1) . " / 5.0 ★\n" .
                               "* **Rider Conduct / Professionalism Attitude:** " . number_format($avgAttitude, 1) . " / 5.0 ★"
                ]);
            }
        }

        // =========================================================================
        // 5. LIVE GENERATIVE AI CORE LAYER (STABLE PRODUCTION ROUTING)
        // =========================================================================
        
        $apiKey = env('GEMINI_API_KEY', 'AQ.Ab8RN6LJbb24_nd2LUMVtRuGp_03mN7GfQ5GTF7Bd9EdBUXTnQ');

        $systemPrompt = "You are the integrated core AI intelligence engine for NinjaVault, an advanced secure smart logistics ecosystem. "
                      . "Your purpose is to answer user questions with high clarity and technical precision. "
                      . "Answer general questions like 'What is the objective of this dashboard?' by explaining that NinjaVault provides comprehensive tracking, "
                      . "parcel metrics analytics, shipping volumes, and customer satisfaction monitoring in one secure management dashboard. "
                      . "Keep your answers highly professional, clean, and concise.";

        try {
            // STEP A: Request clean, stable baseline 'gemini-1.5-flash' without version suffixes
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json'
            ])->post("https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=" . trim($apiKey), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemPrompt . "\n\nUser Question: " . $rawMessage]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $aiOutputData = $response->json();
                $generatedText = $aiOutputData['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (!empty($generatedText)) {
                    return response()->json(['reply' => trim($generatedText)]);
                }
            }

            // STEP B: Fallback deployment handling if the enterprise account requires upgraded modern engines (2.5 generation)
            if ($response->status() == 404) {
                $backupResponse = Http::withoutVerifying()->withHeaders([
                    'Content-Type' => 'application/json'
                ])->post("https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=" . trim($apiKey), [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $systemPrompt . "\n\nUser Question: " . $rawMessage]
                            ]
                        ]
                    ]
                ]);

                if ($backupResponse->successful()) {
                    $aiOutputData = $backupResponse->json();
                    $generatedText = $aiOutputData['candidates'][0]['content']['parts'][0]['text'] ?? null;

                    if (!empty($generatedText)) {
                        return response()->json(['reply' => trim($generatedText)]);
                    }
                }

                return response()->json([
                    'reply' => "### Google API Handshake Interrupted\n" .
                               "* **HTTP Status Code:** " . $backupResponse->status() . "\n" .
                               "* **Raw Gateway Explanation:** " . $backupResponse->body()
                ]);
            }

            // Diagnostic reporting block for unexpected infrastructure messaging
            return response()->json([
                'reply' => "### Google API Handshake Interrupted\n" .
                           "* **HTTP Status Code:** " . $response->status() . "\n" .
                           "* **Raw Gateway Explanation:** " . $response->body()
            ]);

        } catch (\Exception $ex) {
            return response()->json([
                'reply' => "### Runtime Network Exception Traced\n" .
                           "* **Exception Message:** " . $ex->getMessage()
            ]);
        }
    }
}