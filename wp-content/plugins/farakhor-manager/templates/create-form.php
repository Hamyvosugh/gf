<?php
// templates/create-form.php
?>
<div class="wrap">
    <h1> ساخت فراخور جدید</h1>
    
    <form id="create-farakhor-form" class="create-form">
        <input type="hidden" name="action" value="save_farakhor_data">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('farakhor_nonce'); ?>">
        
        <table class="form-table">
            <tr>
                <th><label for="persian_day"> تاریخ ایرانی </label></th>
                <td>
                    <input type="text" id="persian_day" name="persian_day" class="regular-text" required>
                </td>
            </tr>
            
            <tr>
                <th><label for="gregorian_day"> تاریخ میلادی</label></th>
                <td>
                    <input type="date" id="gregorian_day" name="gregorian_day" class="regular-text" required>
                </td>
            </tr>
            
            <tr>
                <th><label for="event_title"> عنوان فراخور</label></th>
                <td>
                    <input type="text" id="event_title" name="event_title" class="regular-text" required>
                </td>
            </tr>
            
            <tr>
                <th><label for="event_text"> متن کوتاه پشت کارت </label></th>
                <td>
                    <?php 
                    wp_editor('', 'event_text', array(
                        'textarea_name' => 'event_text',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                        'teeny' => true
                    ));
                    ?>
                </td>
            </tr>
            
            <tr>
                <th><label for="event_link"> لینک دهی ، چیزی وارد نشود</label></th>
                <td>
                    <input type="url" id="event_link" name="event_link" class="regular-text">
                </td>
            </tr>
            
            <tr>
                <th><label for="post_link"> لینک اتومات مقاله، چیزی وار د نشود </label></th>
                <td>
                    <input type="url" id="post_link" name="post_link" class="regular-text">
                </td>
            </tr>
            
            <tr>
                <th><label for="categories">دسته بندی</label></th>
                <td>
                    <?php
                    $categories = get_categories(array(
                        'hide_empty' => false,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    ?>
                    <select name="categories[]" id="categories" class="categories-select" multiple="multiple" style="width: 100%;">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo esc_attr($category->name); ?>">
                                <?php echo esc_html($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="tag">مارک زدن</label></th>
                <td>
                    <?php
                    $tags = get_tags(array(
                        'hide_empty' => false,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    ?>
                    <select name="tag[]" id="tag" class="tags-select" multiple="multiple" style="width: 100%;">
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?php echo esc_attr($tag->name); ?>">
                                <?php echo esc_html($tag->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="image">تصویر روی کارت فراخور</label></th>
                <td>
                    <div class="image-upload-wrap">
                        <input type="hidden" id="image" name="image" class="regular-text">
                        <button type="button" class="button upload-image-button" data-target="image">
                            Select Image
                        </button>
                        <div id="image-preview" class="image-preview"></div>
                    </div>
                </td>
            </tr>
            
            <tr>
                <th><label for="logo">لوگو کارت</label></th>
                <td>
                    <div class="image-upload-wrap">
                        <input type="hidden" id="logo" name="logo" class="regular-text">
                        <button type="button" class="button upload-image-button" data-target="logo">
                            Select Logo
                        </button>
                        <div id="logo-preview" class="image-preview"></div>
                    </div>
                </td>
            </tr>
            
            <tr>
                <th><label for="module">فعال غیر فعال</label></th>
                <td>
                    <input type="text" id="module" name="module" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="post_text">پیش نویس</label></th>
                <td>
                    <?php 
                    wp_editor('', 'post_text', array(
                        'textarea_name' => 'post_text',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                        'teeny' => true
                    ));
                    ?>
                </td>
            </tr>

            <tr>
                <th><label for="post_title_1">عنوان پاراگراف نخست</label></th>
                <td>
                    <input type="text" id="post_title_1" name="post_title_1" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="post_text_1"> متن پاراگراف نخست</label></th>
                <td>
                    <?php 
                    wp_editor('', 'post_text_1', array(
                        'textarea_name' => 'post_text_1',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                        'teeny' => true
                    ));
                    ?>
                </td>
            </tr>

            <tr>
                <th><label for="post_title_2"> عنوان پاراگراف دوم</label></th>
                <td>
                    <input type="text" id="post_title_2" name="post_title_2" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="post_text_2"> متن پاراگراف دوم</label></th>
                <td>
                    <?php 
                    wp_editor('', 'post_text_2', array(
                        'textarea_name' => 'post_text_2',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                        'teeny' => true
                    ));
                    ?>
                </td>
            </tr>

            <tr>
                <th><label for="post_title_3"> عنوان پاراگراف سوم</label></th>
                <td>
                    <input type="text" id="post_title_3" name="post_title_3" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="post_text_3"> متن پاراگراف سوم</label></th>
                <td>
                    <?php 
                    wp_editor('', 'post_text_3', array(
                        'textarea_name' => 'post_text_3',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                        'teeny' => true
                    ));
                    ?>
                </td>
            </tr>

            <tr>
                <th><label for="post_title_4"> عنوان پاراگراف چهارم</label></th>
                <td>
                    <input type="text" id="post_title_4" name="post_title_4" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="post_text_4"> متن پاراگراف چهارم  </label></th>
                <td>
                    <?php 
                    wp_editor('', 'post_text_4', array(
                        'textarea_name' => 'post_text_4',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                        'teeny' => true
                    ));
                    ?>
                </td>
            </tr>

            <tr>
                <th><label for="post_title_5"> عنوان پاراگراف پنجم</label></th>
                <td>
                    <input type="text" id="post_title_5" name="post_title_5" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="post_text_5"> متن پاراگراف پنجم  </label></th>
                <td>
                    <?php 
                    wp_editor('', 'post_text_5', array(
                        'textarea_name' => 'post_text_5',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                        'teeny' => true
                    ));
                    ?>
                </td>
            </tr>

            <tr>
                <th><label for="post_title_6"> عنوان پاراگراف ششم</label></th>
                <td>
                    <input type="text" id="post_title_6" name="post_title_6" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="post_text_6"> متن پاراگراف ششم  </label></th>
                <td>
                    <?php 
                    wp_editor('', 'post_text_6', array(
                        'textarea_name' => 'post_text_6',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                        'teeny' => true
                    ));
                    ?>
                </td>
            </tr>

            <tr>
                <th><label for="reference">رفنرنس ها</label></th>
                <td>
                    <?php 
                    wp_editor('', 'reference', array(
                        'textarea_name' => 'reference',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                        'teeny' => true
                    ));
                    ?>
                </td>
            </tr>

            <tr>
                <th><label for="post_image1">تصویر ۱</label></th>
                <td>
                    <div class="image-upload-wrap">
                        <input type="hidden" id="post_image1" name="post_image1" class="regular-text">
                        <button type="button" class="button upload-image-button" data-target="post_image1">
                            Select Image
                        </button>
                        <div id="post_image1-preview" class="image-preview"></div>
                    </div>
                </td>
            </tr>
            
            <tr>
                <th><label for="image_desc_1">زیرنویس تصویر ۱</label></th>
                <td>
                    <input type="text" id="image_desc_1" name="image_desc_1" class="regular-text">
                </td>
            </tr>
            
            <tr>
                <th><label for="post_image2">تصویر ۲</label></th>
                <td>
                    <div class="image-upload-wrap">
                        <input type="hidden" id="post_image2" name="post_image2" class="regular-text">
                        <button type="button" class="button upload-image-button" data-target="post_image2">
                            Select Image
                        </button>
                        <div id="post_image2-preview" class="image-preview"></div>
                    </div>
                </td>
            </tr>
            
            <tr>
                <th><label for="image_desc_2">زیرنویس تصویر ۲</label></th>
                <td>
                    <input type="text" id="image_desc_2" name="image_desc_2" class="regular-text">
                </td>
            </tr>
            
            <tr>
                <th><label for="post_image3">تصویر ۳</label></th>
                <td>
                    <div class="image-upload-wrap">
                        <input type="hidden" id="post_image3" name="post_image3" class="regular-text">
                        <button type="button" class="button upload-image-button" data-target="post_image3">
                            Select Image
                        </button>
                        <div id="post_image3-preview" class="image-preview"></div>
                    </div>
                </td>
            </tr>
            
            <tr>
                <th><label for="image_desc_3">زیرنویس تصویر ۳</label></th>
                <td>
                    <input type="text" id="image_desc_3" name="image_desc_3" class="regular-text">
                </td>
            </tr>
            
            <tr>
                <th><label for="image_desc_4">زیرنویس تصویر ۴</label></th>
                <td>
                    <input type="text" id="image_desc_4" name="image_desc_4" class="regular-text">
                </td>
            </tr>
            
            <tr>
                <th><label for="image_desc_5">زیرنویس تصویر ۵</label></th>
                <td>
                    <input type="text" id="image_desc_5" name="image_desc_5" class="regular-text">
                </td>
            </tr>
            
            <tr>
                <th><label for="post_video">ویدیو</label></th>
                <td>
                    <input type="url" id="post_video" name="post_video" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="post_attribute">ویژگی ها</label></th>
                <td>
                    <input type="text" id="post_attribute" name="post_attribute" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="post_book_url">معرفی کتاب</label></th>
                <td>
                    <input type="url" id="post_book_url" name="post_book_url" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="post_book_img">تصویر کتاب</label></th>
                <td>
                    <div class="image-upload-wrap">
                        <input type="hidden" id="post_book_img" name="post_book_img" class="regular-text">
                        <button type="button" class="button upload-image-button" data-target="post_book_img">
                            Select Image
                        </button>
                        <div id="post_book_img-preview" class="image-preview"></div>
                    </div>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <button type="submit" class="button button-primary">فراخور هم اینک ساخته شود</button>
            <a href="<?php echo admin_url('admin.php?page=farakhor-data'); ?>" class="button">Cancel</a>
        </p>
    </form>
</div>

<style>
.image-preview {
    margin-top: 10px;
}
.image-preview img {
    max-width: 200px;
    height: auto;
}
</style>

