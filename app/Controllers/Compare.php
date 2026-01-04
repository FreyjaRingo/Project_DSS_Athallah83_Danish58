<?php

namespace App\Controllers;

class Compare extends BaseController
{
    public function index()
    {
        return view('layout/header')
             . view('compare')
             . view('layout/footer');
    }

    public function calculate()
    {
        if (! $this->request->is('post')) {
            return redirect()->to('compare');
        }
        $alternatives = $this->request->getPost('alternatives');
        $criteria = $this->request->getPost('criteria');
        $weights = $this->request->getPost('weights');
        $criteriaTypes = $this->request->getPost('criteria_types');
        $matrix = $this->request->getPost('matrix');
        $method1 = $this->request->getPost('method1');
        $method2 = $this->request->getPost('method2');

        $results1 = $this->calculateMethod($method1, $alternatives, $criteria, $weights, $criteriaTypes, $matrix);
        $results2 = $this->calculateMethod($method2, $alternatives, $criteria, $weights, $criteriaTypes, $matrix);

        $data = [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'weights' => $weights,
            'criteriaTypes' => $criteriaTypes,
            'method1' => $method1,
            'method2' => $method2,
            'results1' => $results1,
            'results2' => $results2,
            'comparison' => $this->compareResults($results1, $results2)
        ];

        return view('layout/header')
             . view('compare', $data)
             . view('layout/footer');
    }

    private function calculateMethod($method, $alternatives, $criteria, $weights, $criteriaTypes, $matrix)
    {
        switch ($method) {
            case 'AHP':
                return $this->calculateAHP($alternatives, $criteria, $weights, $matrix);
            case 'TOPSIS':
                return $this->calculateTOPSIS($alternatives, $criteria, $weights, $criteriaTypes, $matrix);
            case 'SAW':
                return $this->calculateSAW($alternatives, $criteria, $weights, $criteriaTypes, $matrix);
            case 'WP':
                return $this->calculateWP($alternatives, $criteria, $weights, $criteriaTypes, $matrix);
            default:
                return [];
        }
    }

    private function calculateAHP($alternatives, $criteria, $weights, $matrix)
    {
        $numAlts = count($alternatives);
        $numCrits = count($criteria);

        // Normalisasi bobot
        $totalWeight = array_sum(array_map('floatval', $weights));
        $normalizedWeights = [];
        for ($i = 0; $i < $numCrits; $i++) {
            $normalizedWeights[$i] = floatval($weights[$i]) / $totalWeight;
        }

        // Hitung skor
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

        usort($finalScores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $finalScores;
    }

    private function calculateTOPSIS($alternatives, $criteria, $weights, $criteriaTypes, $matrix)
    {
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
                // Jika pembagi 0, hasilnya dipaksa 0 agar tidak error
				if ($denominator == 0) {
   					$normalizedMatrix[$i][$j] = 0;
				} else {
   				 $normalizedMatrix[$i][$j] = floatval($matrix[$i][$j]) / $denominator;
				}
            }
        }

        // Matriks terbobot
        $weightedMatrix = [];
        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $weightedMatrix[$i][$j] = $normalizedMatrix[$i][$j] * floatval($weights[$j]);
            }
        }

        // Solusi ideal
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

        // Jarak
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

        // Preferensi
        $preferences = [];
        for ($i = 0; $i < $m; $i++) {
            // Cegah error division by zero
            $denominator = $distanceNegative[$i] + $distancePositive[$i];
            
            if ($denominator == 0) {
                // Jika jarak 0 (data identik), beri nilai 0
                $score = 0; 
            } else {
                $score = $distanceNegative[$i] / $denominator;
            }

            $preferences[] = [
                'name' => $alternatives[$i],
                'score' => $score
            ];
        }

        usort($preferences, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $preferences;
    }

    private function calculateSAW($alternatives, $criteria, $weights, $criteriaTypes, $matrix)
    {
        $m = count($alternatives);
        $n = count($criteria);

        // Normalisasi
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

        // Hitung skor
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

        usort($finalScores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $finalScores;
    }

    private function calculateWP($alternatives, $criteria, $weights, $criteriaTypes, $matrix)
    {
        $m = count($alternatives);
        $n = count($criteria);

        // Normalisasi bobot
        $totalWeight = array_sum(array_map('floatval', $weights));
        $normalizedWeights = [];
        for ($j = 0; $j < $n; $j++) {
            $w = floatval($weights[$j]) / $totalWeight;
            $normalizedWeights[$j] = ($criteriaTypes[$j] === 'benefit') ? $w : -$w;
        }

        // Vektor S
        $vectorS = [];
        for ($i = 0; $i < $m; $i++) {
            $s = 1;
            for ($j = 0; $j < $n; $j++) {
                $s *= pow(floatval($matrix[$i][$j]), $normalizedWeights[$j]);
            }
            $vectorS[$i] = $s;
        }

        // Vektor V
        $totalS = array_sum($vectorS);
        $finalScores = [];
        for ($i = 0; $i < $m; $i++) {
            $finalScores[] = [
                'name' => $alternatives[$i],
                'score' => $vectorS[$i] / $totalS
            ];
        }

        usort($finalScores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $finalScores;
    }

    private function compareResults($results1, $results2)
    {
        $comparison = [];
        
        // Buat mapping rank untuk method 1
        $rank1 = [];
        foreach ($results1 as $index => $result) {
            $rank1[$result['name']] = $index + 1;
        }
        
        // Buat mapping rank untuk method 2
        $rank2 = [];
        foreach ($results2 as $index => $result) {
            $rank2[$result['name']] = $index + 1;
        }
        
        // Bandingkan
        foreach ($rank1 as $name => $r1) {
            $r2 = $rank2[$name];
            $diff = abs($r1 - $r2);
            
            $comparison[] = [
                'name' => $name,
                'rank1' => $r1,
                'rank2' => $r2,
                'difference' => $diff,
                'status' => $diff == 0 ? 'same' : ($diff <= 1 ? 'similar' : 'different')
            ];
        }
        
        return $comparison;
    }
}