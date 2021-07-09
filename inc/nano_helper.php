<?php
	class NanoHelperException extends Exception{}
	class NanoHelper
	{
		// *
		// *  Constants
		// *
		
		const RAWS = [
			'unano' =>                '1000000000000000000',
			'mnano' =>             '1000000000000000000000',
			 'nano' =>          '1000000000000000000000000',
			'knano' =>       '1000000000000000000000000000',
			'Mnano' =>    '1000000000000000000000000000000',
			 'NANO' =>    '1000000000000000000000000000000',
			'Gnano' => '1000000000000000000000000000000000'
		];
		
		const PREAMBLE_HEX = '0000000000000000000000000000000000000000000000000000000000000006';
		const EMPTY32_HEX  = '0000000000000000000000000000000000000000000000000000000000000000';
		const HARDENED     =  0x80000000;
		   
		
		// *
		// *  Denomination to raw
		// *
		
		public static function den2raw($amount, string $denomination): string
		{
			if (!array_key_exists($denomination, self::RAWS)) {
				throw new NanoHelperException("Invalid denomination: $denomination");
			}
			
			$raw_to_denomination = self::RAWS[$denomination];
			
			if ($amount == 0) {
				return '0';
			}
			
			if (strpos($amount, '.')) {
				$dot_pos = strpos($amount, '.');
				$number_len = strlen($amount) - 1;
				$raw_to_denomination = substr($raw_to_denomination, 0, -($number_len - $dot_pos));
			}
			
			$amount = str_replace('.', '', $amount) . str_replace('1', '', $raw_to_denomination);
			
			// Remove useless zeros from left
			while (substr($amount, 0, 1) == '0') {
				$amount = substr($amount, 1);
			}
			
			return $amount;
		}


		// *
		// *  Raw to denomination
		// *
		
		public static function raw2den(string $amount, string $denomination): string
		{
			if (!array_key_exists($denomination, self::RAWS)) {
				throw new NanoHelperException("Invalid denomination: $denomination");
			}
			
			$raw_to_denomination = self::RAWS[$denomination];
			
			if ($amount == '0') {
				return 0;
			}
			
			$prefix_lenght = 39 - strlen($amount);
			
			$i = 0;
			
			while ($i < $prefix_lenght) {
				$amount = '0' . $amount;
				$i++;
			}
			
			$amount = substr_replace($amount, '.', -(strlen($raw_to_denomination)-1), 0);
		
			// Remove useless zeroes from left
			while (substr($amount, 0, 1) == '0' && substr($amount, 1, 1) != '.') {
				$amount = substr($amount, 1);
			}
		
			// Remove useless decimals
			while (substr($amount, -1) == '0') {
				$amount = substr($amount, 0, -1);
			}
			
			// Remove dot if all decimals are zeros
			if (substr($amount, -1) == '.') {
				$amount = substr($amount, 0, -1);
			}
		
			return $amount;
		}
		
		
		// *
		// *  Denomination to denomination
		// *
		
		public static function den2den($amount, string $denomination_from, string $denomination_to): string
		{
			if (!array_key_exists($denomination_from, self::RAWS)) {
				throw new NanoHelperException("Invalid source denomination: $denomination_from");
			}
			if (!array_key_exists($denomination_to, self::RAWS)) {
				throw new NanoHelperException("Invalid target denomination: $denomination_to");
			}
			
			$raw = self::den2raw($amount, $denomination_from);
			
			return self::raw2den($raw, $denomination_to);
		}
	}
	
?>