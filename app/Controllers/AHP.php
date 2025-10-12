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
        $weights = $this->request->getPost('weights'); // Bobot kriteria dari user
        $matrix = $this->request->getPost('matrix'); // Nilai alternatif untuk setiap kriteria

        $numAlts = count($alternatives);
        $numCrits = count($criteria);

        // Normalisasi bobot kriteria agar total = 1
        $totalWeight = array_sum(array_map('floatval', $weights));
        $normalizedWeights = [];
        for ($i = 0; $i < $numCrits; $i++) {
            $normalizedWeights[$i] = floatval($weights[$i]) / $totalWeight;
        }

        // Hitung skor alternatif
        $finalScores = [];
        for ($i = 0; $i < $numAlts; $i++) {
            $score = 0;
            for ($j = 0; $j < $numCrits; $j++) {
                $score += floatval($matrix[$i][$j]) * $normalizedWeights[$j];
            }
            $finalScores[] = [
                'name' => $alternatives[$i],
                'score' => $score
            ];
        }

        // Urutkan berdasarkan skor tertinggi
        usort($finalScores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $data = [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'weights' => $normalizedWeights,
            'results' => $finalScores
        ];

        return view('layout/header')
             . view('ahp', $data)
             . view('layout/footer');
    }
}