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

        // Normalisasi matriks
        $normalizedMatrix = [];
        
        for ($j = 0; $j < $n; $j++) {
            $column = array_column($matrix, $j);
            $column = array_map('floatval', $column);
            
            if ($criteriaTypes[$j] === 'benefit') {
                $max = max($column);
                for ($i = 0; $i < $m; $i++) {
                    $normalizedMatrix[$i][$j] = floatval($matrix[$i][$j]) / $max;
                }
            } else {
                $min = min($column);
                for ($i = 0; $i < $m; $i++) {
                    $normalizedMatrix[$i][$j] = $min / floatval($matrix[$i][$j]);
                }
            }
        }

        // Hitung skor preferensi
        $finalScores = [];
        for ($i = 0; $i < $m; $i++) {
            $score = 0;
            for ($j = 0; $j < $n; $j++) {
                $score += $normalizedMatrix[$i][$j] * floatval($weights[$j]);
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
            'results' => $finalScores
        ];

        return view('layout/header')
             . view('saw', $data)
             . view('layout/footer');
    }
}