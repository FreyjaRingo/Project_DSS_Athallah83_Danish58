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

        // Normalisasi bobot
        $totalWeight = array_sum(array_map('floatval', $weights));
        $normalizedWeights = [];
        for ($j = 0; $j < $n; $j++) {
            $w = floatval($weights[$j]) / $totalWeight;
            // Jika cost, bobot menjadi negatif
            $normalizedWeights[$j] = ($criteriaTypes[$j] === 'benefit') ? $w : -$w;
        }

        // Hitung vektor S
        $vectorS = [];
        for ($i = 0; $i < $m; $i++) {
            $s = 1;
            for ($j = 0; $j < $n; $j++) {
                $s *= pow(floatval($matrix[$i][$j]), $normalizedWeights[$j]);
            }
            $vectorS[$i] = $s;
        }

        // Hitung vektor V (preferensi)
        $totalS = array_sum($vectorS);
        $finalScores = [];
        for ($i = 0; $i < $m; $i++) {
            $finalScores[] = [
                'name' => $alternatives[$i],
                'score' => $vectorS[$i] / $totalS
            ];
        }

        // Urutkan berdasarkan skor tertinggi
        usort($finalScores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $data = [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'results' => $finalScores
        ];

        return view('layout/header')
             . view('wp', $data)
             . view('layout/footer');
    }
}