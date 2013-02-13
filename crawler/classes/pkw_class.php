<?php

/*
Class: ProminentKeywords
Author: Joe Bergevin (joe@seo.com)
Purpose: Simplify the SEMRush API
	
Call Examples: 	
	
*/


/* ######################################################################
   ################# Documentation for Setting Parameters ###############
   ######################################################################
	
	

   
   ######################################################################
   ####################### End Documentation ############################
   ###################################################################### */
	
	

class ProminentKeywords {

	protected $stop_words_str, $all_keywords_list, $keywords_with_counts, $pkw_master;
	protected $sw_file_location = "./stopwords.txt";
	protected $properties = array
			(
				'kw_combo_min' 	=> 1,
				'kw_combo_max'	=> 3,
				'results_limit'	=> 5
			);
	
	
	/*
	Function: __construct
	Expects: Nothing
	Purpose: Pre-set the properties for the api call
	*/
	public function __construct() {
		$stop_words = file($this->sw_file_location, FILE_IGNORE_NEW_LINES);
		$this->stop_words_str = implode("|", $stop_words);
	}

	/*
	Function: __set
	Purpose: set given property
	*/
	public function __set($property = null, $value = null) {
		if ($property !== null) {
			$this->properties[$property] = $value;
		}
		return;
	}

	/*
	Function: setContent
	Purpose: set given property
	*/
	public function setContent($string = null) {
		if ($string !== null) {
			$this->properties['content'] = strtolower($string);
		}
		return;
	}

	/*
	Function: clearPunctuation
	Purpose: Take the punctuation out of the given string.
	*/
	private function clearPunctuation() {
		$this->properties['content'] = preg_replace('/[[:punct:]]/', '', $this->properties['content']); // take out all punctuation.
	}

	/*
	Function: clearStopWords
	Purpose: Take the stop words out of the given string.
	*/
	private function clearStopWords() {
		$this->properties['content'] = trim(preg_replace("/\b($this->stop_words_str)\b/i",'', $this->properties['content']));
		$this->properties['content'] = preg_replace('/\s+/', ' ', $this->properties['content']); // take out all punctuation.
	}

	/*
	Function: return_result
	Purpose: Performs the request and returns the results in array format
	Expects: User must first set the desired properties
	*/
	public function return_result() {
		$this->clearPunctuation();
		$this->clearStopWords();
		$this->createKeywordLists();
		return $this->pkw_master;
	} // end of return_result function

	/*
	Function: createKeywordLists
	Purpose: Create the following lists:
				1. List of all words in content (minus stopwords and punctuation)
				2. Unique Single Keywords (and the count of each)
				3. 
	Expects: User must first set the desired properties
	*/
	public function createKeywordLists() {
		$this->all_keywords_list = explode(' ', $this->properties['content']);
		
		// run only if the 'kw_combo_min' == 1.
		if ($this->properties['kw_combo_min'] == 1) {
			$this->keywords_with_counts = array_count_values($this->all_keywords_list);
			arsort($this->keywords_with_counts);
			$this->pkw_master["1-word-keywords"] = $this->keywords_with_counts;
		}

		for ($combo_num = 2; $combo_num < $this->properties['kw_combo_max'] + 1; $combo_num++) { 
			
			for ($list_num = 0; $list_num < count($this->all_keywords_list) - $combo_num + 1; $list_num++) { 
				
				$current_combo = "";
				
				for ($kw_num = $list_num; $kw_num < $list_num + $combo_num; $kw_num++) { 
					$current_combo .= $this->all_keywords_list[$kw_num] ." ";
				}
				
				$this->pkw_master["$combo_num-word-combos"][] = trim($current_combo);
			}
			
			$this->pkw_master["$combo_num-word-combos"] = array_count_values($this->pkw_master["$combo_num-word-combos"]);
			arsort($this->pkw_master["$combo_num-word-combos"]);
		}

		return;
	}
} // end of ProminentKeywords class

?>