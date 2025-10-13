<?php

namespace App\Controllers;

class WP extends BaseController
{
    public function index()
    {
        return view('layout/header')
             . view('wp')
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

        // STEP 1: Data Input
        $step1 = [
            'matrix' => $matrix,
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'criteriaTypes' => $criteriaTypes,
            'weights' => $weights
        ];

        // STEP 2: Normalisasi Bobot (Perbaikan Bobot)
        $totalWeight = array_sum(array_map('floatval', $weights));
        $normalizedWeights = [];
        $weightDetails = [];
        
        for ($j = 0; $j < $n; $j++) {
            $w = floatval($weights[$j]) / $totalWeight;
            // Jika cost, bobot menjadi negatif
            $finalWeight = ($criteriaTypes[$j] === 'benefit') ? $w : -$w;
            $normalizedWeights[$j] = $finalWeight;
            
            $weightDetails[$criteria[$j]] = [
                'original' => floatval($weights[$j]),
                'normalized' => $w,
                'type' => $criteriaTypes[$j],
                'finalWeight' => $finalWeight
            ];
        }

        $step2 = [
            'totalWeight' => $totalWeight,
            'normalizedWeights' => $normalizedWeights,
            'weightDetails' => $weightDetails
        ];

        // STEP 3: Hitung Vektor S
        $vectorSDetails = [];
        $vectorS = [];
        
        for ($i = 0; $i < $m; $i++) {
            $s = 1;
            $details = [];
            
            for ($j = 0; $j < $n; $j++) {
                $value = floatval($matrix[$i][$j]);
                $power = $normalizedWeights[$j];
                $result = pow($value, $power);
                
                $details[] = [
                    'criteria' => $criteria[$j],
                    'value' => $value,
                    'weight' => $power,
                    'result' => $result
                ];
                
                $s *= $result;
            }
            
            $vectorS[$i] = $s;
            $vectorSDetails[$alternatives[$i]] = [
                'details' => $details,
                'vectorS' => $s
            ];
        }

        $step3 = [
            'vectorS' => $vectorS,
            'vectorSDetails' => $vectorSDetails
        ];

        // STEP 4: Hitung Vektor V (Preferensi)
        $totalS = array_sum($vectorS);
        $vectorVDetails = [];
        $finalScores = [];
        
        for ($i = 0; $i < $m; $i++) {
            $v = $vectorS[$i] / $totalS;
            
            $vectorVDetails[$alternatives[$i]] = [
                'vectorS' => $vectorS[$i],
                'totalS' => $totalS,
                'vectorV' => $v
            ];
            
            $finalScores[] = [
                'name' => $alternatives[$i],
                'score' => $v,
                'vectorS' => $vectorS[$i]
            ];
        }

        $step4 = [
            'totalS' => $totalS,
            'vectorVDetails' => $vectorVDetails
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
             . view('wp', $data)
             . view('layout/footer');
    }
}