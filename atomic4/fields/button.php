<?php
/**
 * @copyright	Copyright (C) 2020 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

class JFormFieldbutton extends JFormField {
	protected $type = 'button';
	public function getInput() {
		return '<script>
					var base_url = window.location.href;
                    var script_id = "createcss";
                    var url = base_url.substring(0, base_url.indexOf(\'administrator\'));
                    var page = \'templates/atomic4/ajax/ajax.php\';
                    var link = url.concat(page); 
                    jQuery.ajax(
                    	{
							url: link,
							type: \'Post\' ,
							data: {script_id:script_id},
							success: function(data){
							if(data == 2) {
								jQuery(\'#jform_params___field1\').closest(\'.control-group\').remove();
							}   
						}
                    });
           		</script>

				<button id="'.$this->id.'" name="'.$this->name.'" type="button" onclick="
                    var base_url = window.location.href;
                    var url = base_url.substring(0, base_url.indexOf(\'administrator\'));
                    var page = \'templates/atomic4/ajax/ajax.php\';
                    var link = url.concat(page); 
                    jQuery.ajax(
                    	{
						url: link,
						type: \'Post\' ,
						data: {id: id},
						success: function(data)  {
						if(data == 1) {
							jQuery(\'#one\').remove();
							jQuery(\'#jform_params___field1\').after(\'<span id=\\\'one\\\'>&nbsp;File created successfully!</span>\');
							jQuery(\'#one\').css(\'color\', \'green\');
							setTimeout( function(){jQuery(\'#one\').hide();} , 5000);
                   	 	}
						else {
								jQuery(\'#one\').remove();
								jQuery(\'#two\').remove();
								jQuery(\'#jform_params___field1\').after(\'<span id=\\\'two\\\'>&nbsp;File already exists!</span>\');
								jQuery(\'#two\').css(\'color\', \'red\');
								setTimeout( function(){jQuery(\'#two\').hide();} , 5000);
						}
                 	} 
                  });">'.'Create template.css file'.
		      '</button>';
		}
}