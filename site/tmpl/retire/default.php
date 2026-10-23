<?php

 
// No direct access to this file
defined('_JEXEC') or die;

if ( $this->unretire ) {
	
	print '<h2>Unretired the following sequences, they are now available for user classification</h2>';
	
	if ( is_array($this->unretired) ) {
		
		foreach ( $this->unretired as $seq ) {
			
			print '<p>';
			print_r ( $seq );
			print '</p>';
		}
	}		
}
else {
	
	print '<h2>Retired the following sequences, they are now considered fully classified and not available for user classification</h2>';
	
	if ( is_array($this->retired) ) {
		
		foreach ( $this->retired as $seq ) {
			
			print '<p>';
			print_r ( $seq );
			print '</p>';
		}
	}		
}

print '<h2>Retire process complete</h2>';

?>


