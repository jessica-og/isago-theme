<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<tr>

    <th>
        Container Width
    </th>

    <td>

        <input
            type="number"
            min="1100"
            max="1600"
            name="isago_theme_settings[container_width]"
            value="<?php echo esc_attr(
                $options['container_width'] ?? 1320
            ); ?>"
        >

        px

    </td>

</tr>



<tr>

    <th>Font Family</th>

    <td>

        <select name="isago_theme_settings[font_family]">

            <?php

       $fonts = [

    'Inter',
    'Nunito',
    'Poppins',
    'Roboto',
    'Open Sans',
    'Montserrat',
    'Manrope',
    'Outfit',

    // Modern / Bold
    'Space Grotesk',
    'Sora',
    'Archivo',
    'Archivo Black',
    'Plus Jakarta Sans',
    'Red Hat Display',
    'Barlow',
    'Barlow Condensed',
    'Bebas Neue',
    'Oswald',
    'Anton',
    'League Spartan',
    'Urbanist',
    'Albert Sans',
    'IBM Plex Sans',
    'Public Sans',
    'Work Sans',
    'Figtree',
    'Rubik',
    'Exo 2',
    'Rajdhani',
    'Titillium Web',
    'Lexend',
    'Noto Sans',

    // Serif
    'Lora',
    'Playfair Display',
    'Merriweather',
    'Cormorant Garamond',
    'Libre Baskerville',
    'Source Serif 4',
    'Crimson Text',
    'DM Serif Display'

];

            foreach ($fonts as $font) :
            ?>

                <option
                    value="<?php echo esc_attr($font); ?>"
                    <?php selected(
                        $options['font_family'] ?? 'Inter',
                        $font
                    ); ?>
                >

                    <?php echo esc_html($font); ?>

                </option>

            <?php endforeach; ?>

        </select>

    </td>

</tr>