<?php

namespace App\Controllers;

class TOPSIS extends BaseController
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

        // Normalisasi matriks
        $normalizedMatrix = [];
        for ($j = 0; $j < $n; $j++) {
            $sumSquares = 0;
            for ($i = 0; $i < $m; $i++) {
                $sumSquares += pow(floatval($matrix[$i][$j]), 2);
            }
            $denominator = sqrt($sumSquares);
            
            for ($i = 0; $i < $m; $i++) {
                $normalizedMatrix[$i][$j] = floatval($matrix[$i][$j]) / $denominator;
            }
        }

        // Matriks ternormalisasi terbobot
        $weightedMatrix = [];
        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $weightedMatrix[$i][$j] = $normalizedMatrix[$i][$j] * floatval($weights[$j]);
            }
        }

        // Solusi ideal positif dan negatif
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

        // Jarak ke solusi ideal
        $distancePositive = [];
        $distanceNegative = [];
        
        for ($i = 0; $i < $m; $i++) {
            $sumPos = 0;
            $sumNeg = 0;
            for ($j = 0; $j < $n; $j++) {
                $sumPos += pow($weightedMatrix[$i][$j] - $idealPositive[$j], 2);
                $sumNeg += pow($weightedMatrix[$i][$j] - $idealNegative[$j], 2);
            }
            $distancePositive[$i] = sqrt($sumPos);
            $distanceNegative[$i] = sqrt($sumNeg);
        }

        // Preferensi relatif
        $preferences = [];
        for ($i = 0; $i < $m; $i++) {
            $preferences[] = [
                'name' => $alternatives[$i],
                'score' => $distanceNegative[$i] / ($distanceNegative[$i] + $distancePositive[$i])
            ];
        }

        // Urutkan berdasarkan preferensi tertinggi
        usort($preferences, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $data = [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'results' => $preferences
        ];

        return view('layout/header')
             . view('topsis', $data)
             . view('layout/footer');
    }
}