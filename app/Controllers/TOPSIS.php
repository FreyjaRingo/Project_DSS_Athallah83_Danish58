<?php

namespace App\Controllers;

class Topsis extends BaseController
{
    public function index()
    {
        return view('layout/header')
             . view('topsis')
             . view('layout/footer');
    }

    public function calculate()
    {
        $alternatives = $this->request->getPost('alternatives');
        $criteria = $this->request->getPost('criteria');
        $weights = $this->request->getPost('weights');
        $criteriaTypes = $this->request->getPost('criteria_types');
        $matrix = $this->request->getPost('matrix');

        $m = count($alternatives);
        $n = count($criteria);

        // STEP 1: Matriks Keputusan
        $step1 = [
            'matrix' => $matrix,
            'alternatives' => $alternatives,
            'criteria' => $criteria
        ];

        // STEP 2: Matriks Ternormalisasi
        $normalizedMatrix = [];
        $columnSums = [];
        
        for ($j = 0; $j < $n; $j++) {
            $sumSquares = 0;
            for ($i = 0; $i < $m; $i++) {
                $sumSquares += pow(floatval($matrix[$i][$j]), 2);
            }
            $columnSums[$j] = sqrt($sumSquares);

            // ERROR HANDLING: jika kolom semua 0, hindari pembagian dengan 0
            if ($columnSums[$j] == 0) {
                for ($i = 0; $i < $m; $i++) {
                    $normalizedMatrix[$i][$j] = 0;
                }
            } else {
                for ($i = 0; $i < $m; $i++) {
                    $normalizedMatrix[$i][$j] = floatval($matrix[$i][$j]) / $columnSums[$j];
                }
            }
        }

        $step2 = [
            'columnSums' => $columnSums,
            'normalizedMatrix' => $normalizedMatrix
        ];

        // STEP 3: Matriks Ternormalisasi Terbobot
        $weightedMatrix = [];
        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $weightedMatrix[$i][$j] = $normalizedMatrix[$i][$j] * floatval($weights[$j]);
            }
        }

        $step3 = [
            'weightedMatrix' => $weightedMatrix,
            'weights' => $weights
        ];

        // STEP 4: Solusi Ideal Positif dan Negatif
        $idealPositive = [];
        $idealNegative = [];
        
        for ($j = 0; $j < $n; $j++) {
            $column = array_column($weightedMatrix, $j);
            if ($criteriaTypes[$j] === 'benefit') {
                $idealPositive[$j] = max($column);
                $idealNegative[$j] = min($column);
            } else {
                $idealPositive[$j] = min($column);
                $idealNegative[$j] = max($column);
            }
        }

        $step4 = [
            'idealPositive' => $idealPositive,
            'idealNegative' => $idealNegative,
            'criteriaTypes' => $criteriaTypes
        ];

        // STEP 5: Jarak Solusi Ideal
        $distancePositive = [];
        $distanceNegative = [];
        $distanceDetails = [];
        
        for ($i = 0; $i < $m; $i++) {
            $sumPos = 0;
            $sumNeg = 0;
            $detailsPos = [];
            $detailsNeg = [];
            
            for ($j = 0; $j < $n; $j++) {
                $diffPos = $weightedMatrix[$i][$j] - $idealPositive[$j];
                $diffNeg = $weightedMatrix[$i][$j] - $idealNegative[$j];
                $sqPos = pow($diffPos, 2);
                $sqNeg = pow($diffNeg, 2);
                
                $detailsPos[] = [
                    'criteria' => $criteria[$j],
                    'value' => $weightedMatrix[$i][$j],
                    'ideal' => $idealPositive[$j],
                    'diff' => $diffPos,
                    'squared' => $sqPos
                ];
                
                $detailsNeg[] = [
                    'criteria' => $criteria[$j],
                    'value' => $weightedMatrix[$i][$j],
                    'ideal' => $idealNegative[$j],
                    'diff' => $diffNeg,
                    'squared' => $sqNeg
                ];
                
                $sumPos += $sqPos;
                $sumNeg += $sqNeg;
            }
            
            $distancePositive[$i] = sqrt($sumPos);
            $distanceNegative[$i] = sqrt($sumNeg);
            
            $distanceDetails[$alternatives[$i]] = [
                'positive' => [
                    'details' => $detailsPos,
                    'sum' => $sumPos,
                    'distance' => $distancePositive[$i]
                ],
                'negative' => [
                    'details' => $detailsNeg,
                    'sum' => $sumNeg,
                    'distance' => $distanceNegative[$i]
                ]
            ];
        }

        $step5 = [
            'distancePositive' => $distancePositive,
            'distanceNegative' => $distanceNegative,
            'distanceDetails' => $distanceDetails
        ];

        // STEP 6: Preferensi Relatif
        $preferences = [];
        for ($i = 0; $i < $m; $i++) {
            $denominator = $distanceNegative[$i] + $distancePositive[$i];

            // ERROR HANDLING: jika denominator = 0, set score = 0
            if ($denominator == 0) {
                $score = 0;
            } else {
                $score = $distanceNegative[$i] / $denominator;
            }

            $preferences[] = [
                'name' => $alternatives[$i],
                'score' => $score,
                'dPlus' => $distancePositive[$i],
                'dMinus' => $distanceNegative[$i]
            ];
        }

        usort($preferences, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $step6 = [
            'preferences' => $preferences
        ];

        $data = [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'step1' => $step1,
            'step2' => $step2,
            'step3' => $step3,
            'step4' => $step4,
            'step5' => $step5,
            'step6' => $step6,
            'results' => $preferences
        ];

        return view('layout/header')
             . view('topsis', $data)
             . view('layout/footer');
    }
}
