<?php

/* Has Category
 * 
 * plugin to test if an entry has a particular category
 */

$plugin_info = array(
  'pi_name' => 'FP Has Category',
  'pi_version' => '1.0',
  'pi_author' => 'Evan Hensleigh',
  'pi_author_url' => 'http://www.futuraprime.net/',
  'pi_description' => 'Runs code if an entry has a particular category',
  'pi_usage' => Fp_has_category::usage()
  );

class Fp_has_category {
	
	var $return_data = "";
	                 
	function Fp_has_category()
	{
		
		global $TMPL, $DB;
		
		$entry_id = $TMPL->fetch_param("entry_id");
		$categories = $TMPL->fetch_param("categories");
		
		# bar-delimited "or"
		$categories = explode("|", $categories);
		
		# sanitize the categories a bit
		foreach ($categories as &$category)
		{
			if (!is_numeric($category))
			{
				# they've inputted a category_url_title, probably, so we convert it
				$query = "SELECT cat_id FROM cp_categories WHERE cat_url_title = $category";
				$results = $DB->query($query);
				
				if($results->num_rows == 1)
				{
					$category = $results->row['cat_id'];
				}
				else
				{
					# not a category url title, then... we can't do anything with it.
					$category = NULL;
				}
			}
		}
		
		
		$query = "SELECT cat_id FROM cp_category_posts WHERE entry_id = $entry_id";
		$results = $DB->query($query);
		
		if($results->num_rows == 0)
		{
			# if it has no categories, it cannot possibly have the requested categories
			return;
		}
		else
		{
			$found_cat = array();
			foreach ($results->result as $row)
			{
				$found_cat[] = $row['cat_id'];
			}
			if(count(array_intersect($found_cat, $categories)) > 0)
			{
				$this->return_data = $TMPL->tagdata;
			}
		}
		
	} # Fp_has_category
	
	# standard EE plugin usage function
	function usage()
	{

		ob_start(); ?>
The Has Category plugin lets you know if an entry has been
assigned a particular category:

{exp:fp_has_category entry_id="{entry_id}" categories="1|4|5"}
	This entry has a category I specified!
{/exp:fp_has_category}


You can use category ids or url titles interchangeably.
		<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;

	} # usage
	
	
} # class Fp_has_category

# end of file