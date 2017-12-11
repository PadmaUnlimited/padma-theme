<?php
class BloxGridRenderer {
	

	private $wrapper;
	
		
	public $blocks = array();
		
	
	private $layout = array();
	
	
	private $rows = array();
	
	
	private $columns = array();
	
	
	private $column_positions = array();
	
	
	private $section_classes = array();
	
	
	private $column_top_tolerance = 30;
	
	
	private $row_top_tolerance = 20;


	public function __construct($blocks, $wrapper) {

		$this->blocks_unsorted = $blocks;
		$this->blocks = $blocks;
		$this->wrapper = $wrapper;

	}
		
		
	private function step_1_sort_blocks_by_position() {
		
		//Sort blocks array from top/left blocks to bottom/right
		@uasort($this->blocks, array(__CLASS__, 'uasort_blocks_by_top_to_left'));
		
	}
	
	
		private function uasort_blocks_by_top_to_left($a, $b) {

			$a_chunk = array_chunk($a, 1);
			$b_chunk = array_chunk($b, 1);

			if ( is_array($a) && isset($a_chunk[0][0]) && is_string($a_chunk[0][0]) )
				$a = $a_chunk[0][0];
				
			if ( is_array($b) && isset($b_chunk[0][0]) && is_string($b_chunk[0][0]) )
				$b = $b_chunk[0][0];

			if ( is_string($a) )
				$a = $this->blocks_unsorted[$a];
				
			if ( is_string($b) )
				$b = $this->blocks_unsorted[$b];
				
			$a_top = $a['position']['top'];
			$a_left = $a['position']['left'];

			$b_top = $b['position']['top'];
			$b_left = $b['position']['left'];

			//If they're the same, which they probably won't be
			if ( $a_top === $b_top && $a_left === $b_left )
				return 0;

			//If top is the same, figure out left position
			if ( $a_top === $b_top ) 
				return ($a_left < $b_left) ? -1 : 1;

			//If top is the different
			return ($a_top < $b_top) ? -1 : 1;

		}
	

	private function step_2_build_rows() {
		
		$prev_block = null;
		$row_count = 0;	
				
		foreach ( $this->blocks as $block_id => $block ) {
			
			$range_beginning = is_array($prev_block) ? $prev_block['position']['top'] - $this->row_top_tolerance : null;
			$range_end = is_array($prev_block) ? $prev_block['position']['top'] + $this->row_top_tolerance : null;
			
			/* If there is a previous block and current top is +-10 of previous block top, then use the existing row. */						
			if ( $range_beginning && blox_in_numeric_range($block['position']['top'], $range_beginning, $range_end) ) {
					
				$this->rows[$row_count][] = $block['id'];
			
			/* Create new row */			
			} else {
				
				$row_count++;
				$this->rows[$row_count] = array($block['id']);
				
			}
										
			$prev_block = $block;
			
		}
				
	}
	
	
	private function step_3_construct_columns() {
		
		$column_id = 0;
		
		foreach ( $this->blocks as $block_id => $block ) {
			
			/* If the block is already in a sub column, then skip it completely. */
			if ( isset($this->blocks_in_sub_columns) && in_array($block['id'], $this->blocks_in_sub_columns) )
				continue;
			
			/* Create a new column for this block if it can't fit into an existing. */
			$column_id++;
			$this->columns[$column_id] = array($block_id);
						
			/* Get the sub column blocks */
			$sub_column_blocks = $this->step_3a_extract_sub_columns($block['id']);			
			
			/* No sub column blocks, we can skip everything below. */
			if ( !is_array($sub_column_blocks) )
				continue;
											
			$sub_column_row_id = 1;
			$prev_sub_column_offset = 0;
			$existing_sub_columns = array();

			/**
			 * Go through sub column blocks and place them in the necessary sub columns.
			 **/			
			foreach ( $sub_column_blocks as $sub_column_block_id ) {
				
				$sub_column_block = $this->blocks[$sub_column_block_id];
									
				/**
				 * If the width of this sub column block is the same as the origin, then do not create a sub column.  
				 * It belongs in the regular column.
				 **/
				$sub_column = $sub_column_block['dimensions']['width'] == $block['dimensions']['width'] ? false : true;
				
				/* Remove sub column blocks from their original column */
				$this->step_3b_remove_block_from_columns($sub_column_block_id);
								
				/**
				 * Handle the sub column ID.
				 * 
				 * If a new row is caught, then add a new ID to the column.
				 * 
				 * Also, if the previous sub column block is above the origin block and the new one is below the origin, then create a new row
				 **/
				if ( isset($prev_sub_column_top) && $prev_sub_column_top + $prev_sub_column_height < $block['position']['top'] )
					if ( $sub_column_block['position']['top'] > $block['position']['top'] + $block['dimensions']['height'] )
						$sub_column_row_id++;
						
				$sub_column_original = 'sub-column-' . $sub_column_block['dimensions']['width'] . ':' . $sub_column_block['position']['left'];
				$sub_column_id = $sub_column_original . '-' . $sub_column_row_id;
				
				$origin_width = (int)$block['dimensions']['width'];
				$origin_left = (int)$block['position']['left'];

				if ( $prev_sub_column_offset + $sub_column_block['dimensions']['width'] - $origin_left > $origin_width ) {
				
					$prev_sub_column_offset = 0;
				
					if ( !in_array($sub_column_original, $existing_sub_columns) )
						$sub_column_row_id++;
					
				}
																																		
				/* Add block to new column */
				if ( $sub_column ) {
					
					$existing_sub_columns[] = $sub_column_original;
												
					if ( !isset($this->columns[$column_id][$sub_column_id]) )
						$this->columns[$column_id][$sub_column_id] = array();
																												
					$this->columns[$column_id][$sub_column_id][] = $sub_column_block_id;
											
				} else {
											
					/* Add sub column blocks to the current column */
					$this->columns[$column_id][] = $sub_column_block_id;
																
				}
				
				$prev_sub_column_offset = (int)$sub_column_block['dimensions']['width'] + (int)$sub_column_block['position']['left'];
				
				$prev_sub_column_top = (int)$sub_column_block['position']['top'];
				$prev_sub_column_height = (int)$sub_column_block['dimensions']['height'];
				
			} /* End: foreach $sub_column_blocks as $sub_column_block_id */
			
			/**
			 * BEGIN BATSHIT INSANE STUFF
			 * 
			 * If the sub column blocks form only a column rather than sub columns, then remove the blocks 
			 * from the $this->blocks_in_sub_columns array if conditions are met.
			 * 
			 * Also, since the block is removed from $this->blocks_in_sub_columns, there will be extraneous columns created.  
			 * Those need to be purged as well.
			 * 
			 * Also, the step_3c_modify_rows_for_sub_column_above_origin() method should only run if sub columns are being used.
			 **/
			$sub_columns = false;
			
			foreach ( $this->columns[$column_id] as $block_or_sub_column )
				if ( is_array($block_or_sub_column) )
					$sub_columns = true;
				
			if ( $sub_columns ) {
								
				/* If the column begins with a sub column rather than the origin, then we need to modify the original rows. */
				$this->step_3c_modify_rows_for_sub_column_above_origin($block, $this->blocks[reset($sub_column_blocks)], $sub_column_blocks);
				
			} elseif ( !$sub_columns && isset($this->blocks_in_sub_columns) ) {
								
				foreach ( $this->columns[$column_id] as $block_or_sub_column )
					blox_remove_from_array($this->blocks_in_sub_columns, $block_or_sub_column);
					
				/* Purge extraneous old columns */
				foreach ( $this->columns as $test_column_id => $blocks_or_sub_columns ) {
					
					if ( $column_id == $test_column_id )
						continue;
						
					foreach ( $blocks_or_sub_columns as $index => $block_id_or_sub_column ) {
						
						if ( $block_id_or_sub_column == $block['id'] )
							unset($this->columns[$test_column_id][$index]);
							
						if ( count($this->columns[$test_column_id]) === 0 )
							unset($this->columns[$test_column_id]);
						
					}

				}
					
			}	
			/* END BATSHIT INSANE STUFF */		
											
			/* Sort the columns by block position */
			@uasort($this->columns[$column_id], array(__CLASS__, 'uasort_blocks_by_top_to_left'));
							
			/* Remove the keys from the sub columns and blocks (even though they shouldn't exist) */
			if ( isset($this->columns[$column_id]) )
				$this->columns[$column_id] = array_values($this->columns[$column_id]);
			
		}		
		
		/* Remove the unnecessary grouping IDs from the columns */
		$this->columns = array_values($this->columns);
		
	}
	
		
		private function step_3a_extract_sub_columns($origin_block_id) {

			if ( isset($this->blocks_in_sub_columns) && in_array($origin_block_id, $this->blocks_in_sub_columns) )
				return false;

			$matches = array();

			$origin = $this->blocks[$origin_block_id];

			/**
			 * Find the first batch of matches that fits inside the main origin block as far as left position and width goes.
			 **/
			foreach ( $this->blocks as $check ) {

				/* Make sure that we're not checking the origin block */
				if ( $origin['id'] == $check['id'] )
					continue;
					
				/* Do not add any blocks that are already in sub columns */
				if ( isset($this->blocks_in_sub_columns) && in_array($check['id'], $this->blocks_in_sub_columns) )
					continue;
					
				/* Block left position must not be more than the origin block's left and width combined */
				if ( $check['position']['left'] > $origin['position']['left'] + $origin['dimensions']['width'] )
					continue;

				/* Block left position must be either equal to or more than the origin block's left */
				if ( $check['position']['left'] < $origin['position']['left'] )
					continue;

				/* Block left and width still must not be more than the origin block's left and width */
				if ( $check['position']['left'] + $check['dimensions']['width'] > $origin['position']['left'] + $origin['dimensions']['width'] )
					continue;

				$matches[] = $check['id'];
			
			}
			
			
			/**
			 * If there are no matches then stop now
			 **/
			if ( count($matches) === 0 )
				return null;			
			
						
			/**
			 * Checks matches ABOVE the origin to make sure they can actually fit in the sub column.
			 * 
			 * If there's a block on the left or right of the match and it interrupts what could be a sub column, then the match is removed.
			 **/
			$bad_matches = array();
			$match_row_ids = array();
			
			/* Get the row ID for every match. */
			foreach ( $matches as $match_id )
				$match_row_ids[$match_id] = $this->get_block_row($match_id);

			/* Go through the rows that the matches are in to find the blocks adjacent to the matches. */
			foreach ( $match_row_ids as $match_block_id => $match_row_id ) {
				
				reset($this->rows[$match_row_id]);
											
				while ( $current_match = current($this->rows[$match_row_id]) ) {
					
					if ( $current_match == $match_block_id ) {
						
						$neighbors = blox_array_key_neighbors($this->rows[$match_row_id], key($this->rows[$match_row_id]));
																		
						$left_block = (is_string($neighbors['prev']) && isset($this->blocks[$neighbors['prev']])) ? $this->blocks[$neighbors['prev']] : null;
						$right_block = ( is_string($neighbors['next']) && isset($this->blocks[$neighbors['next']])) ? $this->blocks[$neighbors['next']] : null;
						
						$origin_block_left = $origin['position']['left'];
						$origin_block_width = $origin['dimensions']['width'];
						
						if ( $left_block ) {
							
							$left_block_left = $left_block['position']['left'];
							$left_block_width = $left_block['dimensions']['width'];
							
						}

						if ( $right_block ) {
							
							$right_block_left = $right_block['position']['left'];
							$right_block_width = $right_block['dimensions']['width'];
							
						}
						
						/* Check if the left block goes outside of the origin (while still being in it) */
						if ( $left_block && $left_block_left + $left_block_width > $origin_block_left && $left_block_left < $origin_block_left )
							$bad_matches[$match_block_id] = 'left-block-outside-origin';
							
						/* Check if right block goes outside of the origin (while still being in it) */
						if ( $right_block && $right_block_left < $origin_block_left + $origin_block_width )
							if ( $right_block_left + $right_block_width > $origin_block_left + $origin_block_width )
								$bad_matches[$match_block_id] = 'right-block-outside-origin';
											
						if ( isset($bad_matches[$match_block_id]) )
							blox_remove_from_array($matches, $match_block_id);
							
						break;
						
					}
					
					next($this->rows[$match_row_id]);
					
				}								
								
			}
									
			
			/**
			 * Make sure the blocks are touching vertically.
			 * 
			 * First, the blocks BELOW the origin are checked.
			 * Then, the blocks ABOVE the origin are checked.  The $matches array has to be reversed for this process.
			 **/
			$bad_matches = array();
			
			/* Matches BELOW the origin */
			$prev_row_block_id = $origin['id'];

			foreach ( $matches as $match_id ) {
								
				$match_block = $this->blocks[$match_id];
				$prev_row_block = $this->blocks[$prev_row_block_id];
				$is_first_match = $match_id == reset($matches);
								
				if ( $match_block['position']['top'] < $origin['position']['top'] )
					continue;				
				
				/**
				 * If the block is in the same row as the last, then let it slide as long as it's not the first block
				 **/
				if ( !($prev_row_block['position']['top'] === $match_block['position']['top']) || $is_first_match ) {	
					
					/* Block's top value must be greater the previous block's top + height combined to know that it's below the previous block. */
					if ( $match_block['position']['top'] <= $prev_row_block['dimensions']['height'] + $prev_row_block['position']['top'] )
						$bad_matches[$match_id] = 'not below previous';

					/* Block being checked is no more than the column top tolarence below than the previous row block's height */
					if ( $match_block['position']['top'] > $prev_row_block['dimensions']['height'] + $prev_row_block['position']['top'] + $this->column_top_tolerance )
						$bad_matches[$match_id] = 'below previous block and tolerance';
					
				}
												
				/* If it's bad match, remove it from the matches array. */	
				if ( isset($bad_matches[$match_id]) )
					blox_remove_from_array($matches, $match_id);
				
				/* Only change the previous row block ID if the match's top is larger than the previous */	
				elseif ( $match_block['position']['top'] > $prev_row_block['position']['top'] )												
					$prev_row_block_id = $match_id;	
												
			}
			
			/* Matches ABOVE the origin */ 
			$reversed_matches = array_reverse($matches);
			$prev_row_block_id = $origin['id'];
						
			foreach ( $reversed_matches as $match_id ) {
								
				$match_block = $this->blocks[$match_id];
				$prev_row_block = $this->blocks[$prev_row_block_id];
				$is_first_match = $match_id == reset($reversed_matches);
				
				if ( $match_block['position']['top'] > $origin['position']['top'] )
					continue;
								
				/**
				 * If the block is in the same row as the last, then let it slide as long as it's not the first block
				 **/
				if ( !($prev_row_block['position']['top'] === $match_block['position']['top']) || $is_first_match ) {	
										
					/* Block's top value + height must be less than the top of the previous block to know that it's above the previous block. */
					if ( $match_block['position']['top'] + $match_block['dimensions']['height'] > $prev_row_block['position']['top'] )
						$bad_matches[$match_id] = 'not above previous';

					/* Block being checked is no more than the column top tolarence above than the previous row block's top */
					if ( $match_block['position']['top'] + $match_block['dimensions']['height'] < $prev_row_block['position']['top'] - $this->column_top_tolerance )
						$bad_matches[$match_id] = 'above previous block and tolerance';
					
				}
								
				/* If it's bad match, remove it from the matches array. */	
				if ( isset($bad_matches[$match_id]) )	
					blox_remove_from_array($matches, $match_id);
				
				/* Only change the previous row block ID if the match's top is larger than the previous */	
				elseif ( $match_block['position']['top'] < $prev_row_block['position']['top'] )												
					$prev_row_block_id = $match_id;	
												
			}			
						
				
			/**
			 * If there are no matches then stop now
			 **/
			if ( count($matches) === 0 )
				return null;				
			
			/**
			 * Check that the matches and the origin block actually qualify as a sub column.
			 * 
			 * 1. There must be a block that's on the side of the main column.  
			 *    This first check needs to check that the block isn't anywhere in the column (using left checks)
			 * 
			 * 2. Check that the block starts within the height of the origin block
			 **/
			$check_1 = false;
			$check_2 = false;
						
			foreach ( $this->blocks as $block ) {
				
				/* Make sure we're not checking the origin block or a match. */
				if ( in_array($block['id'], $matches) || $block['id'] == $origin_block_id )
					continue;
					
				$check_left = $block['position']['left'];
				$check_top = $block['position']['top'];
				$check_width = $block['dimensions']['width'];
				$check_height = $block['dimensions']['height'];
				
				$origin_width = $origin['dimensions']['width'];
				$origin_left = $origin['position']['left'];
				
				$origin_height = $origin['dimensions']['height'];
				$origin_top = $origin['position']['top'];
				
				$top_block_top = $this->blocks[reset($matches)]['position']['top'];
				$top_block_height = $this->blocks[reset($matches)]['dimensions']['height'];
				
				/* In case there are sub columns above the origin, then we need to use the top block (above the origin) for check #2 */					
				$height_check_block = ( $top_block_top < $origin_top ) ? $top_block_height : $origin_height;
				$top_check_block = ( $top_block_top < $origin_top ) ? $top_block_top : $origin_top;					
				
				/* #1 */
				if ( $check_left < $origin_left || $check_left >= $origin_left + $origin_width )
					$check_1 = true;
					
				/* #2 */
				if ( $check_top >= $top_check_block && $check_top < $top_check_block + $height_check_block )
					$check_2 = true;

			}
						
			/* Verify that both checks passed. */
			if ( !($check_1 && $check_2) )
				return false;
								
			/* Add any matches to the already-in-sub-column array */		
			$this->blocks_in_sub_columns = isset($this->blocks_in_sub_columns) ? array_merge($this->blocks_in_sub_columns, $matches) : $matches;
						
			/* Return the matches or null if there are none. */
			return count($matches) > 0 ? $matches : null;

		}

	
		private function step_3b_remove_block_from_columns($block_id_to_remove) {
						
			foreach ( $this->columns as $column_id => $column_blocks ) {
								
				if ( in_array($block_id_to_remove, $column_blocks) ) {
					
					blox_remove_from_array($this->columns[$column_id], $block_id_to_remove);
																				
					/* Remove the column if it's empty */
					if ( empty($this->columns[$column_id]) )
						unset($this->columns[$column_id]);
											
					return true;
					
				}
				
			}
			
			return false;
			
		}
	
		
		private function step_3c_modify_rows_for_sub_column_above_origin($origin_block, $first_block, array $sub_column_blocks) {
			
			if ( $origin_block['position']['top'] < $first_block['position']['top'] )
				return false;
				
			/* Get the position of the origin block and the first block so their position in the rows can be switched around. */
			foreach ( $this->rows as $row_id => $row_blocks ) {

				foreach ( $row_blocks as $row_block_id ) {

					if ( $row_block_id === $origin_block['id'] )
						$origin_block_row_id = $row_id;

					elseif ( $row_block_id === $first_block['id'] )
						$first_block_row_id = $row_id;

				}

			}

			/* Find any other blocks that are in the same sub column row as the first block */
			$sub_column_blocks_above_origin = array();

			foreach ( $sub_column_blocks as $sub_column_block_id ) {
				
				$test_block = $this->blocks[$sub_column_block_id];

				if ( $test_block['position']['top'] + $test_block['dimensions']['height'] >= $origin_block['position']['top'] )
					continue;

				$sub_column_blocks_above_origin[] = $sub_column_block_id;

			}
			

			$first_block_position_in_row = array_search($first_block['id'], $this->rows[$first_block_row_id]);
			
			blox_array_insert($this->rows[$first_block_row_id], array($origin_block['id']), $first_block_position_in_row);				

			foreach ( $sub_column_blocks_above_origin as $block_id )
				$this->step_3d_remove_block_from_rows($block_id);
				
			blox_remove_from_array($this->rows[$origin_block_row_id], $origin_block['id']);
			
			if ( count($this->rows[$first_block_row_id]) === 0 )
				unset($this->rows[$first_block_row_id]);
				
			if ( count($this->rows[$origin_block_row_id]) === 0 )
				unset($this->rows[$origin_block_row_id]);
			
		}
		
		
		private function step_3d_remove_block_from_rows($block_id_to_remove) {
						
			foreach ( $this->rows as $row_id => $row_blocks ) {
								
				if ( in_array($block_id_to_remove, $row_blocks) ) {
					
					blox_remove_from_array($this->rows[$row_id], $block_id_to_remove);
																				
					/* Remove the row if it's empty */
					if ( empty($this->rows[$row_id]) )
						unset($this->rows[$row_id]);
											
					return true;
					
				}
				
			}
			
			return false;
			
		}


	private function step_4_fetch_column_row_positions() {

		foreach ( $this->columns as $column => $blocks ) {

			foreach ( $blocks as $block_id ) {				

				foreach ( $this->rows as $row => $blocks ) {

					if ( in_array($block_id, $blocks) ) {	
						$this->column_positions[$column] = $row;
						break;
					}

				}

				if ( isset($this->column_positions[$column]) )
					break;

			}

		}

	}
		

	private function step_5_add_columns_to_rows() {
		
		//Throw columns back into rows if the column exists
		foreach ( $this->column_positions as $column => $row ) {
			
			if ( !isset($this->columns[$column]) )
				continue;
				
			$this->layout[$row][$column] = $this->columns[$column];

		}
		
		//Make sure the column remains in the proper order (left to right)
		foreach ( $this->layout as $row => $row_columns )
			@uasort($this->layout[$row], array(__CLASS__, 'uasort_columns_by_left'));
			
		//Resort row order
		ksort($this->layout, SORT_NUMERIC);		
								
	}
	
	
		private function uasort_columns_by_left($a, $b) {
			
			foreach ( $a as $block_or_sub_column_a )
				if ( is_string($block_or_sub_column_a) && $a = $block_or_sub_column_a )
					break;
					
			foreach ( $b as $block_or_sub_column_b )
				if ( is_string($block_or_sub_column_b) && $b = $block_or_sub_column_b )
					break;

			$a = $this->blocks[$a];
			$b = $this->blocks[$b];
				
			$a_left = $a['position']['left'];
			$b_left = $b['position']['left'];

			//If they're the same, which they probably won't be
			if ( $a_left === $b_left )
				return 0;

			return ($a_left < $b_left) ? -1 : 1;

		}
	
	
	private function step_6_add_section_classes() {	
		
		$row_count = 1;
						
		foreach ( $this->layout as $row_index => $columns ) {
			
			$this->section_classes[$row_index] = array();
			
			/* Rows */
				$this->section_classes[$row_index]['classes'][] = 'row';
				$this->section_classes[$row_index]['classes'][] = 'row-' . $row_count;
				$row_count++;
			
			/* Columns */
				/* Set up variables */
				$previous_column_offset = 0; //Reset previous offset variable for every new row
				$column_count = 1;

				/* Go through each column to calculate the margin and width */
				foreach ( $columns as $column_index => $column_contents ) {

					$this->section_classes[$row_index][$column_index] = array();
								
					/* Find the first block in the column contents to calculate the main column width and margin */
						foreach ( $column_contents as $block_index_or_sub_index => $block_id_or_sub_contents ) {
					
							if ( is_string($block_id_or_sub_contents) && isset($this->blocks[$block_id_or_sub_contents]) ) {
						
								$first_block_in_column = $this->blocks[$block_id_or_sub_contents];
								break;
						
							} else
								continue;
					
						}
					/* End finding first block in column */	
					
				
					/* Calculate main column width and margin */
						/**
						 * If it's the first column, do not worry about margin to the left, there's no blocks pushing from the left to worry about
						 * 
						 * Else if it's not, take the left and width of previous column (in the form of $previous_column_offset) 
						 * and subtract that from the left of the current column
						 **/
						$this->section_classes[$row_index][$column_index]['classes'][] = 'column';
						$this->section_classes[$row_index][$column_index]['classes'][] = 'column-' . $column_count;
						$this->section_classes[$row_index][$column_index]['classes'][] = 'grid-left-' . ((int)$first_block_in_column['position']['left'] - (int)$previous_column_offset); 
						$this->section_classes[$row_index][$column_index]['classes'][] = 'grid-width-' . ((int)$first_block_in_column['dimensions']['width']);

						$this->section_classes[$row_index][$column_index]['width'] = ((int)$first_block_in_column['dimensions']['width']);
						$this->section_classes[$row_index][$column_index]['absolute-left'] = (int)$first_block_in_column['position']['left'];

						$previous_column_offset = (int)$first_block_in_column['dimensions']['width'] + (int)$first_block_in_column['position']['left'];
						$column_count++;
					/* End main column width and margin calculation */
					
									
					/* Handle Sub Columns */
						$sub_column_count = 1;
						$prev_sub_column_offset = 0;
						$this->section_classes[$row_index][$column_index]['sub-columns'] = array();
				
						foreach ( $column_contents as $sub_index => $sub_contents ) {
					
							/* If the item is a block then skip it. */
							if ( is_string($sub_contents) )
								continue;
							
							$sub_column_block = $this->blocks[reset($sub_contents)];
						
							$main_column_absolute_left = $this->section_classes[$row_index][$column_index]['absolute-left'];
							$main_column_width = $this->section_classes[$row_index][$column_index]['width'];
												
							/* Reset the sub column count and offset if it's a new sub column row */						
							if ( $prev_sub_column_offset + (int)$sub_column_block['dimensions']['width'] - $main_column_absolute_left > $main_column_width ) {
							
								$sub_column_count = 1;
								$prev_sub_column_offset = 0;
							
							}
						
							/* The left offset only needs to be subtracted from the first sub column in the sub column row */
							$main_column_left_offset = $sub_column_count === 1 ? $main_column_absolute_left : 0;
																			
							$this->section_classes[$row_index][$column_index]['sub-columns'][$sub_index]['classes'] = array(
								'sub-column',
								'sub-column-' . $sub_column_count,
								'column',
								'column-' . $sub_column_count,
								'grid-width-' . $sub_column_block['dimensions']['width'],
								'grid-left-' . ($sub_column_block['position']['left'] - $prev_sub_column_offset - $main_column_left_offset)
							);
						
							$prev_sub_column_offset = (int)$sub_column_block['dimensions']['width'] + (int)$sub_column_block['position']['left'];
							$sub_column_count++;
											
						}
				
						if ( count($this->section_classes[$row_index][$column_index]['sub-columns']) === 0 )
							unset($this->section_classes[$row_index][$column_index]['sub-columns']);
					/* End Sub Column Handling */

				}
				/* End $columns foreach */

		}
		/* End $this->layout foreach */

	}


		private function get_block_row($block_id) {
			
			foreach ( $this->rows as $row_id => $row_blocks )
				foreach ( $row_blocks as $row_block_id )
					if ( $row_block_id == $block_id )
						return $row_id;

			return null;
			
		}


	private function step_7_finalize() {

		$this->finalized_layout = array();

		foreach ( $this->layout as $row_index => $row_columns ) {

			$this->finalized_layout[$row_index] = array(
				'type' => 'row',
				'classes' => $this->section_classes[$row_index]['classes'],
				'columns' => array()
			);

			$current_row =& $this->finalized_layout[$row_index];

			foreach ( $row_columns as $column_index => $column_content ) {

				$current_row['columns'][$column_index] = array(
					'type' => 'column',
					'width' => $this->section_classes[$row_index][$column_index]['width'],
					'classes' => $this->section_classes[$row_index][$column_index]['classes'],
					'contents' => array()
				);

				$current_column =& $current_row['columns'][$column_index];
													
				foreach ( $column_content as $block_or_sub_index => $block_id_or_sub_content ) {
					
					if ( !is_array($block_id_or_sub_content) ) {

						$block = $this->blocks[$block_id_or_sub_content];

						$current_column['contents'][$block_or_sub_index] = array(
							'type' => 'block',
							'block' => $block
						);

						/* If block CSS class bubbling is enabled then add that to row and column */
							$block_settings = blox_get('settings', $block);
						
							if ( blox_get('css-classes-bubble', $block_settings, false) === true && blox_get('css-classes', $block_settings) ) {


								$current_row['classes'] = array_merge($current_row['classes'], explode(' ' , blox_get('css-classes', $block_settings)));
								$current_column['classes'] = array_merge($current_column['classes'], explode(' ' , blox_get('css-classes', $block_settings)));

							}
						
					} else {

						$current_column['contents'][$block_or_sub_index] = array(
							'type' => 'sub-column',
							'classes' => $this->section_classes[$row_index][$column_index]['sub-columns'][$block_or_sub_index]['classes'],
							'blocks' => array()
						);

						$current_sub_column =& $current_column['contents'][$block_or_sub_index];
						
						foreach ( $block_id_or_sub_content as $sub_block_id ) {

							$this->blocks[ $sub_block_id ]['parent-column-width'] = $current_column['width'];

							$current_sub_column['blocks'][] = $this->blocks[ $sub_block_id ];

							/* If block CSS class bubbling is enabled then add that to row and column and sub column */
								$block_settings = blox_get('settings', $this->blocks[ $sub_block_id ]);

								if ( blox_get('css-classes-bubble', $block_settings, false, true) === true && blox_get('css-classes', $block_settings) ) {

									$current_row['classes'] = array_merge($current_row['classes'], explode(' ' , blox_get('css-classes', $block_settings)));
									$current_column['classes'] = array_merge($current_column['classes'], explode(' ' , blox_get('css-classes', $block_settings)));
									$current_sub_column['classes'] = array_merge($current_sub_column['classes'], explode(' ' , blox_get('css-classes', $block_settings)));

								}
						
						}				
						
					}		
				
				}
									
			}
			
		}		

		return $this->finalized_layout;

	}


	public function process() {

		$this->step_1_sort_blocks_by_position();
		$this->step_2_build_rows();
		$this->step_3_construct_columns();
		$this->step_4_fetch_column_row_positions();
		$this->step_5_add_columns_to_rows();
		$this->step_6_add_section_classes();
		$this->step_7_finalize();

	}


	public function render_grid() {
				
		$this->process();

		echo '<div class="grid-container clearfix">' . "\n";

		do_action('blox_grid_container_open', $this->wrapper);
	
		foreach ( $this->finalized_layout as $row_index => $row ) {

			echo "\n" . '<section class="' . implode(' ',  array_unique(array_filter($row['classes']))) . '">' . "\n";

				do_action('blox_block_row_open', $this->wrapper);
			
				foreach ( $row['columns'] as $column_index => $column ) {
						
					echo "\n" . '<section class="' . implode(' ', array_unique(array_filter($column['classes']))) . '">' . "\n";

					do_action('blox_block_column_open', $this->wrapper);
														
					foreach ( $column['contents'] as $index => $block_or_sub_column ) {
						
						if ( blox_get('type', $block_or_sub_column) == 'block' ) {
														
							BloxBlocks::display_block(blox_get('block', $block_or_sub_column), 'grid-renderer');
							
						} elseif ( blox_get('type', $block_or_sub_column) == 'sub-column' ) {
														
							echo "\n" . '<section class="' . implode(' ', array_unique(array_filter($block_or_sub_column['classes']))) . '">' . "\n";

							do_action('blox_block_sub_column_open', $this->wrapper);
							
							foreach ( $block_or_sub_column['blocks'] as $sub_block )
								BloxBlocks::display_block($sub_block, 'grid-renderer');
								
							do_action('blox_block_sub_column_column', $this->wrapper);
							
							echo "\n" . '</section>' . "\n";
							
						}		
					
					}

					do_action('blox_block_column_close', $this->wrapper);
				
					echo "\n" . '</section>' . "\n";
										
				}

				do_action('blox_block_row_close', $this->wrapper);
		
			echo "\n" . '</section>' . "\n\n";
			
		}		

		do_action('blox_grid_container_close', $this->wrapper);
	
		echo "\n" . '</div>' . "\n\n";
		
	}


}