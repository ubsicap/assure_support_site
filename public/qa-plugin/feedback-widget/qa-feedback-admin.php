<?php
class qa_feedback_admin
{

    function allow_template($template)
    {
        return ($template != 'admin');
    }

    function option_default($option)
	{
		switch ($option) {
			case 'feedback_widget_enabled':
				return 1; // true
			default:
		}
	}
}
