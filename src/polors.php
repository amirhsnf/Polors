<?php class polors
{
    public function generate_colors($colors)
    {
        if(count($colors) != 8) return false;
        list($light, $tertiary, $primary, $secondary, $dark, $success, $warning, $danger) = $colors;
        if(!$primary){
            if($secondary){
                $generate = $this->get_complementary($this->hex_to_hsv($secondary));
                $primary = $this->hsv_to_hex($generate[0]);
                if(!$tertiary)
                    $tertiary = $this->hsv_to_hex($generate[1]);
            }
            elseif($tertiary){
                $generate = $this->get_complementary($this->hex_to_hsv($tertiary));
                $primary = $this->hsv_to_hex($generate[0]);
                $secondary = $this->hsv_to_hex($generate[1]);
            }
            elseif($light){
                $primary = $this->hsv_to_hex($this->get_primary_from_dark_or_light($this->hex_to_hsv($light)));
                $generate = $this->get_complementary($this->hex_to_hsv($primary));
                $secondary = $this->hsv_to_hex($generate[0]);
                $tertiary = $this->hsv_to_hex($generate[1]);
            }
            elseif($dark){
                $primary = $this->hsv_to_hex($this->get_primary_from_dark_or_light($this->hex_to_hsv($dark)));
                $generate = $this->get_complementary($this->hex_to_hsv($primary));
                $secondary = $this->hsv_to_hex($generate[0]);
                $tertiary = $this->hsv_to_hex($generate[1]);
            }
            else{
                $primary = $this->hsv_to_hex($this->get_random_primary());
                $generate = $this->get_complementary($this->hex_to_hsv($primary));
                $secondary = $this->hsv_to_hex($generate[0]);
                $tertiary = $this->hsv_to_hex($generate[1]);
            }
        }
        if(!$secondary){
            $generate = $this->get_complementary($this->hex_to_hsv($primary));
            $secondary = $this->hsv_to_hex($generate[0]);
            if(!$tertiary)
                $tertiary = $this->hsv_to_hex($generate[1]);
        }
        if(!$tertiary){
            $generate = $this->get_complementary($this->hex_to_hsv($primary));
            $tertiary = $this->hsv_to_hex($generate[0]);
        }
        if(!$light){
            $light = $this->hsv_to_hex($this->get_light_from_primary($this->hex_to_hsv($primary)));
        }
        if(!$dark){
            $dark = $this->hsv_to_hex($this->get_dark_from_primary($this->hex_to_hsv($primary)));
        }
        if(!$success){
            $success = $this->hsv_to_hex([rand(75, 150), rand(25, 100), rand(30, 85)]);
        }
        if(!$warning){
            $warning = $this->hsv_to_hex([rand(25, 55), rand(50, 100), rand(80, 100)]);
        }
        if(!$danger){
            $danger = $this->hsv_to_hex([rand(0, 10), rand(70, 100), rand(65, 100)]);
        }
        return [$light, $tertiary, $primary, $secondary, $dark, $success, $warning, $danger];
    }
    public function get_dark_from_primary($hsv)
    {
        $h = abs($hsv[0] + rand(-10, 10)) % 360;
        $s = rand(0, 50);
        $lighter = rand(1, 10) == 10;
        $v = $lighter ? rand(0, 40) : rand(0, 25);
        return [$h, $s, $v];
    }
    public function get_light_from_primary($hsv)
    {
        $h = abs($hsv[0] + rand(-10, 10)) % 360;
        $darker = rand(1, 10) == 10;
        $s = $darker ? rand(0, 25) : rand(0, 10);
        $v = $darker ? rand(75, 100) : rand(90, 100);
        return [$h, $s, $v];
    }
    public function get_random_primary()
    {
        $h = rand(0, 359);
        $s = rand(10, 40);
        $v = rand(40, 70);
        return [$h, $s, $v];
    }
    public function get_primary_from_dark_or_light($hsv)
    {
        $h = $hsv[0];
        $s = rand(10, 40);
        $v = rand(40, 70);
        return [abs($h + rand(-10, 10)) % 360, $s, $v];
    }
    public function get_complementary($hsv)
    {
//        $methods = [[0, 180], [150, 210], [120, 240], [30, 330], [10, 350]];
        $methods = [[30, 180], [180, 330], [30,330], [105, 210], [30, 90], [90, 330], [30, 120]];
        $method = $methods[rand(0, count($methods) - 1)];
        $method = $methods[6];
        shuffle($method);
        list($h, $s, $v) = $hsv;
        $sr = $this->get_sv_ranges($s);
        $vr = $this->get_sv_ranges($v);
        return [
            [
                $this->change_hue($h, $method[0], true),
                $this->change_sv($s, $sr[0], true),
                $this->change_sv($v, $vr[0], true)
            ],
            [
                $this->change_hue($h, $method[1], true),
                $this->change_sv($s, $sr[1], true),
                $this->change_sv($v, $vr[1], true)
            ]
        ];
    }
    public function change_hue($h, $value, $shake = false): int
    {
        $h += $value;
        if($shake)
            $h += rand(-5, 5);
        $h = $h % 360;
        if($h < 0)
            $h = 360 - $h;
        return $h;
    }
    public function change_sv($sv, $range = null, $shake = false): int
    {
        if($range)
            $sv = rand($range[0], $range[1]);
        if($shake)
            $sv += rand(-5, 5);
        if($sv < 0) $s = 0;
        if($sv > 100) $s = 100;
        return $sv;
    }
    public function get_sv_ranges($sv): array | false
    {
        $level = floor($sv / 10);
        if($level == 10) $level--;
        $range_1 = $level == 0 ? 2 : $level - 1;
        $range_2 = $level == 9 ? 7 : $level + 1;
        return [
            [$range_1 * 10, ($range_1 * 10) + 9],
            [$range_2 * 10, ($range_2 * 10) + 9],
        ];
    }
    public function hex_to_hsv($hex): array
    {
        return $this->rgb_to_hsv($this->hex_to_rgb($hex));
    }
    public function hex_to_rgb($hex): array
    {
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        return [$r, $g, $b];
    }
    public function rgb_to_hsv($rgb): array
    {
        $r = $rgb[0] / 255;
        $g = $rgb[1] / 255;
        $b = $rgb[2] / 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $v = round(100 * $max);
        $d = $max - $min;
        if($d == 0){
            return [0, 0, $v];
        }
        $s = round(100 * ($d / $max));
        if($r == $min)
            $h = 3 - (($g - $b) / $d);
        elseif($b == $min)
            $h = 1 - (($r - $g) / $d);
        else
            $h = 5 - (($b - $r) / $d);
        $h = round(60 * $h);
        return [$h, $s, $v];
    }
    public function hsv_to_rgb($hsv): array
    {
        list($h, $s, $v) = $hsv;
        $rgb = [0, 0, 0];
        for($i = 0; $i < 4; $i++){
            if(abs($h - $i * 120) < 120){
                $d = max(60, abs($h - $i * 120));
                $rgb[$i % 3] = 1 - (($d - 60) / 60);
            }
        }
        $max = max($rgb);
        $factor = 255 * ($v / 100);
        for($i = 0; $i < 3; $i++){
            $rgb[$i] = round(($rgb[$i] + ($max - $rgb[$i]) * (1 - $s / 100)) * $factor);
        }
        return $rgb;
    }
    public function rgb_to_hex($rgb): string
    {
        return sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
    }
    public function hsv_to_hex($hsv): string
    {
        return $this->rgb_to_hex($this->hsv_to_rgb($hsv));
    }
}
