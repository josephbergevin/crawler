<?php

include_once('/classes/simplehtmldom_1_5/simple_html_dom.php');

$a_tag_str = "<a href=\"/\"><img src=\"http://www.hapari.com/skin1/images/hapari-logo.png\" alt=\"Hapari Swimwear Logo\" /></a>";

$dom = str_get_html( $a_tag_str );

print_r( $dom->root->children[0]->children[0]->outertext );

/*foreach ( $dom as $key => $value ) {
	echo "<p>KEY: $key | VALUE: $value->outertext</p>";

}
*/


?>