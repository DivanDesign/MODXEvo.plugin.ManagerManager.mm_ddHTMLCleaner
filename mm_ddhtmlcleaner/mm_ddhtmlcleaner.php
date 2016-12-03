<?php
/**
 * mm_ddHTMLCleaner
 * @version 1.0.4 (2014-03-14)
 * 
 * @desc A widget for the plugin ManagerManager. It removes forbidden HTML attributes and styles from document fields and TVs when required.
 * 
 * @uses MODXEvo.plugin.ManagerManager >= 0.6.
 * 
 * @param $fields {string_commaSeparated} — The name(s) of the document fields (or TVs) which the widget is applied to. @required
 * @param $roles {string_commaSeparated} — Roles that the widget is applied to (when this parameter is empty then widget is applied to the all roles). Default: ''.
 * @param $templates {string_commaSeparated} — Templates IDs for which the widget is applying (empty value means the widget is applying to all templates). Default: ''.
 * @param $validAttrsForAllTags {string_commaSeparated} — Attributes that can be applied to all HTML tags. The others will be removed. Default: 'title,class'.
 * @param $validStyles {string_commaSeparated} — Valid styles that have not to be cut from the style attribute. Default: 'word-spacing'.
 * @param $validAttrs {string_JSON_object} — A JSON object containing valid attributes (set as comma separated values) which have not to be removed from corresponding HTML tags (set as keys). Default: '{"img":"src,alt,width,height","a":"href,target"}'.
 * 
 * @event OnDocFormPrerender
 * @event OnDocFormRender
 * 
 * @link http://code.divandesign.biz/modx/mm_ddhtmlcleaner/1.0.4
 * 
 * @copyright 2013–2014 DivanDesign {@link http://www.DivanDesign.biz }
 */

function mm_ddHTMLCleaner(
	$fields,
	$roles = '',
	$templates = '',
	$validAttrsForAllTags = 'title,class',
	$validStyles = 'word-spacing',
	$validAttrs = '{"img":"src,alt,width,height","a":"href,target"}'
){
	if (!useThisRule($roles, $templates)){return;}
	
	global $modx;
	$e = &$modx->Event;
	
	if ($e->name == 'OnDocFormPrerender'){
		$widgetDir = $modx->config['site_url'].'assets/plugins/managermanager/widgets/mm_ddhtmlcleaner/';
		
		$output = includeJsCss($widgetDir.'jQuery.ddHTMLCleaner-0.2.min.js', 'html', 'jQuery.ddHTMLCleaner', '0.2');
		$output .= includeJsCss($widgetDir.'jQuery.ddMM.mm_ddHTMLCleaner.js', 'html', 'jQuery.ddMM.mm_ddHTMLCleaner', '1.0.1');
		
		$e->output($output);
	}else if ($e->name == 'OnDocFormRender'){
		global $mm_fields, $content;
		
		if ($content['contentType'] != 'text/html'){return;}
		
		$fields = getTplMatchedFields($fields);
		if ($fields == false){return;}
		
		$selectors = array();
		
		foreach ($fields as $field){
			$selectors[] = $mm_fields[$field]['fieldtype'].'[name=\"'.$mm_fields[$field]['fieldname'].'\"]';
		}
		
		$output = '//---------- mm_ddHTMLCleaner :: Begin -----'.PHP_EOL;
		
		$output .=
'
$j.ddMM.mm_ddHTMLCleaner.addInstance("'.implode(',', $selectors).'", {
	validAttrsForAllTags: "'.$validAttrsForAllTags.'",
	validAttrs: '.$validAttrs.',
	validStyles: "'.$validStyles.'"
});
';
		
		$output .= '//---------- mm_ddHTMLCleaner :: End -----'.PHP_EOL;
		
		$e->output($output);
	}
}
?>