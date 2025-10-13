<?php

namespace App\Controllers;

class AHP extends BaseController
{
    public function index()
    {
        return view('layout/header')
             . view('ahp')
             . view('layout/footer');
    }

    public function calculate()
    {
        $alternatives = $this->request->getPost('alternatives');
        $criteria = $this->request->getPost('criteria');
        $weights = $this->request->getPost('weights');
        $matrix = $this->request->getPost('matrix');

        $numAlts = count($alternatives);
        $numCrits = count($criteria);

        // STEP 1: Data Input
        $step1 = [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'weights' => $weights,
            'matrix' => $matrix
        ];

        // STEP 2: Normalisasi bobot kriteria
        $totalWeight = array_sum(array_map('floatval', $weights));
        $normalizedWeights = [];
        for ($i = 0; $i < $numCrits; $i++) {
            $normalizedWeights[$i] = floatval($weights[$i]) / $totalWeight;
        }

        $step2 = [
            'totalWeight' => $totalWeight,
            'normalizedWeights' => $normalizedWeights
        ];

        // STEP 3: Perhitungan skor untuk setiap alternatif
        $calculations = [];
        $finalScores = [];
        
        for ($i = 0; $i < $numAlts; $i++) {
            $score = 0;
            $details = [];
            
            for ($j = 0; $j < $numCrits; $j++) {
                $value = floatval($matrix[$i][$j]);
                $weight = $normalizedWeights[$j];
                $contribution = $value * $weight;
                
                $details[] = [
                    'criteria' => $criteria[$j],
                    'value' => $value,
                    'weight' => $weight,
                    'contribution' => $contribution
                ];
                
                $score += $contribution;
            }
            
            $calculations[$alternatives[$i]] = [
                'details' => $details,
                'totalScore' => $score
            ];
            
            $finalScores[] = [
                'name' => $alternatives[$i],
                'score' => $score
            ];
        }

        // STEP 4: Urutkan hasil
        usort($finalScores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $data = [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'weights' => $weights,
            'step1' => $step1,
            'step2' => $step2,
            'calculations' => $calculations,
            'normalizedWeights' => $normalizedWeights,
            'results' => $finalScores
        ];

        return view('layout/header')
             . view('ahp', $data)
             . view('layout/footer');
    }
}