<?php

namespace App\Controllers;

class SAW extends BaseController
{
    public function index()
    {
        return view('layout/header')
             . view('saw')
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
            'criteria' => $criteria,
            'criteriaTypes' => $criteriaTypes,
            'weights' => $weights
        ];

        // STEP 2: Tentukan Max/Min per Kriteria
        $maxMinValues = [];
        for ($j = 0; $j < $n; $j++) {
            $column = array_column($matrix, $j);
            $column = array_map('floatval', $column);
            $maxMinValues[$j] = [
                'max' => max($column),
                'min' => min($column),
                'type' => $criteriaTypes[$j]
            ];
        }

        $step2 = [
            'maxMinValues' => $maxMinValues
        ];

        // STEP 3: Normalisasi Matriks
        $normalizedMatrix = [];
        $normalizationDetails = [];
        
        for ($i = 0; $i < $m; $i++) {
            $normalizationDetails[$alternatives[$i]] = [];
            for ($j = 0; $j < $n; $j++) {
                $value = floatval($matrix[$i][$j]);
                
                if ($criteriaTypes[$j] === 'benefit') {
                    $normalized = $value / $maxMinValues[$j]['max'];
                    $formula = "$value / {$maxMinValues[$j]['max']}";
                } else {
                    $normalized = $maxMinValues[$j]['min'] / $value;
                    $formula = "{$maxMinValues[$j]['min']} / $value";
                }
                
                $normalizedMatrix[$i][$j] = $normalized;
                $normalizationDetails[$alternatives[$i]][$criteria[$j]] = [
                    'original' => $value,
                    'formula' => $formula,
                    'normalized' => $normalized
                ];
            }
        }

        $step3 = [
            'normalizedMatrix' => $normalizedMatrix,
            'normalizationDetails' => $normalizationDetails
        ];

        // STEP 4: Perhitungan Skor (Preferensi)
        $scoreDetails = [];
        $finalScores = [];
        
        for ($i = 0; $i < $m; $i++) {
            $score = 0;
            $details = [];
            
            for ($j = 0; $j < $n; $j++) {
                $contribution = $normalizedMatrix[$i][$j] * floatval($weights[$j]);
                $details[] = [
                    'criteria' => $criteria[$j],
                    'normalized' => $normalizedMatrix[$i][$j],
                    'weight' => floatval($weights[$j]),
                    'contribution' => $contribution
                ];
                $score += $contribution;
            }
            
            $scoreDetails[$alternatives[$i]] = [
                'details' => $details,
                'totalScore' => $score
            ];
            
            $finalScores[] = [
                'name' => $alternatives[$i],
                'score' => $score
            ];
        }

        $step4 = [
            'scoreDetails' => $scoreDetails
        ];

        // Urutkan
        usort($finalScores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $data = [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'step1' => $step1,
            'step2' => $step2,
            'step3' => $step3,
            'step4' => $step4,
            'results' => $finalScores
        ];

        return view('layout/header')
             . view('saw', $data)
             . view('layout/footer');
    }
}