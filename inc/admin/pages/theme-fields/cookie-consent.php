<?php

if (!defined('ABSPATH')) {
    exit;
}

?>


<!-- =====================================================
Cookie Consent
===================================================== -->

<tr>

<th colspan="2">

<hr>

<h2>Cookie Consent</h2>

</th>

</tr>


<tr>

<th>

Enable Cookie Banner

</th>

<td>

<label>

<input
type="checkbox"
name="isago_cookie_settings[enabled]"
value="1"
<?php checked(

    isago_get_option(
        'isago_cookie_settings',
        'enabled',
        1
    ),

    1

); ?>>

Enable cookie consent banner

</label>

</td>

</tr>


<tr>

<th>

Cookie Banner Title

</th>

<td>

<input
class="regular-text"
type="text"
name="isago_cookie_settings[title]"
value="<?php

echo esc_attr(

    isago_get_option(

        'isago_cookie_settings',

        'title',

        'We use cookies'

    )

);

?>">

</td>

</tr>


<tr>

<th>

Cookie Description

</th>

<td>

<textarea
rows="4"
cols="60"
name="isago_cookie_settings[description]"><?php

echo esc_textarea(

    isago_get_option(

        'isago_cookie_settings',

        'description',

        'Isago uses cookies to improve your experience, understand site usage, and support relevant advertising.'

    )

);

?></textarea>

</td>

</tr>


<tr>

<th>

Accept Button

</th>

<td>

<input
class="regular-text"
type="text"
name="isago_cookie_settings[accept_text]"
value="<?php

echo esc_attr(

    isago_get_option(

        'isago_cookie_settings',

        'accept_text',

        'Accept All'

    )

);

?>">

</td>

</tr>


<tr>

<th>

Reject Button

</th>

<td>

<input
class="regular-text"
type="text"
name="isago_cookie_settings[reject_text]"
value="<?php

echo esc_attr(

    isago_get_option(

        'isago_cookie_settings',

        'reject_text',

        'Reject Non-Essential'

    )

);

?>">

</td>

</tr>


<tr>

<th>

Manage Button

</th>

<td>

<input
class="regular-text"
type="text"
name="isago_cookie_settings[manage_text]"
value="<?php

echo esc_attr(

    isago_get_option(

        'isago_cookie_settings',

        'manage_text',

        'Manage Preferences'

    )

);

?>">

</td>

</tr>


<tr>

<th>

Analytics Label

</th>

<td>

<input
class="regular-text"
type="text"
name="isago_cookie_settings[analytics_label]"
value="<?php

echo esc_attr(

    isago_get_option(

        'isago_cookie_settings',

        'analytics_label',

        'Analytics'

    )

);

?>">

</td>

</tr>


<tr>

<th>

Analytics Description

</th>

<td>

<textarea
rows="3"
cols="60"
name="isago_cookie_settings[analytics_description]"><?php

echo esc_textarea(

    isago_get_option(

        'isago_cookie_settings',

        'analytics_description',

        'Helps us understand how visitors use Isago so we can improve the website.'

    )

);

?></textarea>

</td>

</tr>


<tr>

<th>

Marketing Label

</th>

<td>

<input
class="regular-text"
type="text"
name="isago_cookie_settings[marketing_label]"
value="<?php

echo esc_attr(

    isago_get_option(

        'isago_cookie_settings',

        'marketing_label',

        'Advertising & Marketing'

    )

);

?>">

</td>

</tr>


<tr>

<th>

Marketing Description

</th>

<td>

<textarea
rows="3"
cols="60"
name="isago_cookie_settings[marketing_description]"><?php

echo esc_textarea(

    isago_get_option(

        'isago_cookie_settings',

        'marketing_description',

        'Allows advertising and marketing services to provide relevant content and measure advertising performance.'

    )

);

?></textarea>

</td>

</tr>


<tr>

<th>

Consent Duration

</th>

<td>

<input
type="number"
min="1"
max="365"
name="isago_cookie_settings[cookie_days]"
value="<?php

echo esc_attr(

    isago_get_option(

        'isago_cookie_settings',

        'cookie_days',

        180

    )

);

?>">

days

</td>

</tr>