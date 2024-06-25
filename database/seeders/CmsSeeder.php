<?php

namespace Database\Seeders;

use App\Models\Cms;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cms = new Cms();

        //About page
        $cms->about_brand_logo = 'frontend/assets/images/all-img/brand-1.png';
        $cms->about_brand_logo1 = 'frontend/assets/images/all-img/brand-2.png';
        $cms->about_brand_logo2 = 'frontend/assets/images/all-img/brand-1.png';
        $cms->about_brand_logo3 = 'frontend/assets/images/all-img/brand-2.png';
        $cms->about_brand_logo4 = 'frontend/assets/images/all-img/brand-1.png';
        $cms->about_brand_logo5 = 'frontend/assets/images/all-img/brand-2.png';
        $cms->payment_logo1 = 'frontend/assets/images/all-img/payment_logo1.png';
        $cms->payment_logo2 = 'frontend/assets/images/all-img/payment_logo2.png';
        $cms->payment_logo3 = 'frontend/assets/images/all-img/payment_logo3.png';
        $cms->payment_logo4 = 'frontend/assets/images/all-img/payment_logo4.png';
        $cms->payment_logo5 = 'frontend/assets/images/all-img/payment_logo5.png';
        $cms->about_banner_img = 'frontend/assets/images/banner/about-banner-1.jpg';
        $cms->about_banner_img1 = 'frontend/assets/images/banner/about-banner-2.jpg';
        $cms->about_banner_img2 = 'frontend/assets/images/banner/about-banner-3.jpg';
        $cms->about_banner_img3 = 'frontend/assets/images/banner/about-banner-4.jpg';
        $cms->mission_image = 'frontend/assets/images/banner/about-banner-5.png';
        $cms->candidate_image = 'frontend/assets/images/all-img/cta-1.png';
        $cms->employers_image = 'frontend/assets/images/all-img/cta-2.png';
        $cms->contact_map = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.2278794778554!2d90.34898411536302!3d23.77489829375602!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c1e1938cc90b%3A0xbcfacb6b89117685!2sZakir%20Soft%20-%20Innovative%20Software%20%26%20Web%20Development%20Solutions!5e0!3m2!1sen!2sbd!4v1629355846404!5m2!1sen!2sbd" width="100%" height="536" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
        $cms->register_page_image = 'frontend/assets/images/all-img/auth-img.png';
        $cms->login_page_image = 'frontend/assets/images/all-img/auth-img.png';
        $cms->page403_image = 'frontend/assets/images/banner/error-banner.png';

        $cms->page404_image = 'frontend/assets/images/banner/error-banner.png';

        $cms->page500_image = 'frontend/assets/images/banner/error-banner.png';

        $cms->page503_image = 'frontend/assets/images/banner/error-banner.png';

        $cms->comingsoon_image = 'frontend/assets/images/all-img/coming-banner.png';

        $cms->footer_phone_no = '319-555-0115';
        $cms->footer_facebook_link = 'https://www.facebook.com/zakirsoft';
        $cms->footer_instagram_link = 'https://www.instagram.com/zakirsoft';
        $cms->footer_twitter_link = 'https://www.twitter.com/zakirsoft';
        $cms->footer_youtube_link = 'https://www.youtube.com/zakirsoft';
        $cms->privary_page = '
        <p>Welcome to Jobpilot, a product of Templatecookie. This Privacy Policy outlines how we collect, use, disclose, and protect your personal information. By accessing or using Jobpilot, you agree to the terms outlined in this policy.</p>
    
        <h2>Information We Collect</h2>
        
        <h6>Personal Information:</h6>
        <ul><li>When you use Jobpilot, we may collect personal information such as your name, email address, and contact details.</li></ul>
        
        <h6>User-Generated Content:</h6>
        <ul><li>We may collect information you provide when using our services, such as job preferences, resume details, and any other content you submit.</li></ul>
        
        <h6>Device Information:</h6>
        <ul><li>We may collect information about the device you use to access Jobpilot, including device type, operating system, and unique device identifiers.</li></ul>
        
        <h6>Log Data:</h6>
        <ul><li>Like many websites and applications, we automatically collect log data, which may include your IP address, browser type, pages visited, and time spent on the platform.</li></ul>
    
        <h2>How We Use Your Information</h2>
        <p>We use the collected information for the following purposes:</p>

        <h6>Providing and Improving Services:</h6>
        <ul>
            <li>To offer, operate, maintain, and improve Jobpilot\'s features and functionality.</li>
        </ul>

        <h6>Personalization:</h6>
        <ul>
            <li>To tailor our services to your preferences and provide a personalized user experience.</li>
        </ul>

        <h6>Communication:</h6>
        <ul>
            <li>To communicate with you, respond to inquiries, and send relevant notifications.</li>
        </ul>

        <h6>Analytics:</h6>
        <ul>
            <li>To analyze usage patterns, monitor trends, and gather demographic information to improve our services.</li>
        </ul>
    
        <h2>Information Sharing</h2>
        <p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as detailed in this Privacy Policy.</p>
        
        <h6>Service Providers:</h6>
        <ul><li>We may share information with third-party service providers to perform functions on our behalf, such as hosting, analytics, and customer support.</li></ul>
        
        
        <h6>Legal Compliance:</h6>
        <ul><li>We may disclose information when required by law or to protect our rights, privacy, safety, or property.</li></ul>


        <h2>Gallery Access Permission</h2>
        <p>Jobpilot may request access to your device\'s gallery to provide specific functionality, such as uploading a profile picture or attaching images to job applications.</p>
    
        <h6>Types of Data Collected:</h6>
        <ul><li>Photos and images stored in your device\'s gallery.</li></ul>
        
        <h6>Purpose of Collection:</h6>
        <ul><li>To enhance your profile, attach relevant images to job applications, etc.</li></ul>
        
        <h6>Consent:</h6>
        <ul><li>By using Jobpilot, you grant us permission to access your device\'s gallery for the specified purposes. You can manage or revoke this permission through your device settings.</li></ul>
        
        <h6>Retention:</h6>
        <ul><li>We will only retain images from your gallery for as long as necessary to fulfill the purpose for which they were collected or as required by applicable laws.</li></ul>
        
        <h6>Security:</h6>
        <ul><li>We take appropriate measures to secure and protect any images or information accessed from your gallery.</li></ul>
        
        <h6>Sharing:</h6>
        <ul><li>We do not share the images accessed from your gallery with third parties, except as outlined in this Privacy Policy.</li></ul>
    
        <h2>Account Deletion</h2>
        <p>Users have the option to delete their accounts through the following URLs:</p>
        <ul>
            <li><strong>Candidate Account Deletion:</strong> <a href="https://jobpilot.templatecookie.com/candidate/settings">https://jobpilot.templatecookie.com/candidate/settings</a></li>
            <li><strong>Company Account Deletion:</strong> <a href="https://jobpilot.templatecookie.com/company/settings">https://jobpilot.templatecookie.com/company/settings</a></li>
        </ul>
    
        <h2>Contact Us</h2>
        <p>If you have questions or concerns about this Privacy Policy, please contact us at <a href="mailto:hello+jobpilot@templatecookie.com">hello+jobpilot@templatecookie.com</a>.</p>';
        $cms->terms_page = '<h2>01. Terms &amp; Condition</h2><p>Praesent placerat dictum elementum. Nam pulvinar urna vel lectus maximus, eget faucibus turpis hendrerit. Sed iaculis molestie arcu, et accumsan nisi. Quisque molestie velit vitae ligula luctus bibendum. Duis sit amet eros mollis, viverra ipsum sed, convallis sapien. Donec justo erat, pulvinar vitae dui ut, finibus euismod enim. Donec velit tortor, mollis eu tortor euismod, gravida lacinia arcu.</p><ul><li>In ac turpis mi. Donec quis semper neque. Nulla cursus gravida interdum.</li><li>Curabitur luctus sapien augue, mattis faucibus nisl vehicula nec. Mauris at scelerisque lorem. Nullam tempus felis ipsum, sagittis malesuada nulla vulputate et.</li><li>Aenean vel metus leo. Vivamus nec neque a libero sodales aliquam a et dolor.</li><li>Vestibulum rhoncus sagittis dolor vel finibus.</li><li>Integer feugiat lacus ut efficitur mattis. Sed quis molestie velit.</li></ul><h2>02. Limitations</h2><p>Praesent placerat dictum elementum. Nam pulvinar urna vel lectus maximus, eget faucibus turpis hendrerit. Sed iaculis molestie arcu, et accumsan nisi. Quisque molestie velit vitae ligula luctus bibendum. Duis sit amet eros mollis, viverra ipsum sed, convallis sapien. Donec justo erat, pulvinar vitae dui ut, finibus euismod enim. Donec velit tortor, mollis eu tortor euismod, gravida lacinia arcu.</p><ul><li>In ac turpis mi. Donec quis semper neque. Nulla cursus gravida interdum.</li><li>Curabitur luctus sapien augue.</li><li>mattis faucibus nisl vehicula nec, Mauris at scelerisque lorem.</li><li>Nullam tempus felis ipsum, sagittis malesuada nulla vulputate et. Aenean vel metus leo.</li><li>Vivamus nec neque a libero sodales aliquam a et dolor.</li></ul><h2>03. Security</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ex neque, elementum eu blandit in, ornare eu purus. Fusce eu rhoncus mi, quis ultrices lacus. Phasellus id pellentesque nulla. Cras erat nisi, mattis et efficitur et, iaculis a lacus. Fusce gravida augue quis leo facilisis.</p><h2>04. Privacy Policy</h2><p>Praesent non sem facilisis, hendrerit nisi vitae, volutpat quam. Aliquam metus mauris, semper eu eros vitae, blandit tristique metus. Vestibulum maximus nec justo sed maximus. Vivamus sit amet turpis sem. Integer vitae tortor ac ex scelerisque facilisis ac vitae urna. In hac habitasse platea dictumst. Maecenas imperdiet tortor arcu, nec tincidunt neque malesuada volutpat.</p><ul><li>In ac turpis mi. Donec quis semper neque. Nulla cursus gravida interdum.</li><li>Mauris at scelerisque lorem. Nullam tempus felis ipsum, sagittis malesuada nulla vulputate et.</li><li>Aenean vel metus leo.</li><li>Vestibulum rhoncus sagittis dolor vel finibus.</li><li>Integer feugiat lacus ut efficitur mattis. Sed quis molestie velit.</li></ul><p>Fusce rutrum mauris sit amet justo rutrum, ut sodales lorem ullamcorper. Aliquam vitae iaculis urna. Nulla vitae mi vel nisl viverra ullamcorper vel elementum est. Mauris vitae elit nec enim tincidunt aliquet. Donec ultrices nulla a enim pulvinar, quis pulvinar lacus consectetur. Donec dignissim, risus nec mollis efficitur, turpis erat blandit urna, eget elementum lacus lectus eget lorem.</p><p><br>&nbsp;</p>';
        $cms->refund_page = '<h2>01. Refund Policy</h2><p>Praesent placerat dictum elementum. Nam pulvinar urna vel lectus maximus, eget faucibus turpis hendrerit. Sed iaculis molestie arcu, et accumsan nisi. Quisque molestie velit vitae ligula luctus bibendum. Duis sit amet eros mollis, viverra ipsum sed, convallis sapien. Donec justo erat, pulvinar vitae dui ut, finibus euismod enim. Donec velit tortor, mollis eu tortor euismod, gravida lacinia arcu.</p><ul><li>In ac turpis mi. Donec quis semper neque. Nulla cursus gravida interdum.</li><li>Curabitur luctus sapien augue, mattis faucibus nisl vehicula nec. Mauris at scelerisque lorem. Nullam tempus felis ipsum, sagittis malesuada nulla vulputate et.</li><li>Aenean vel metus leo. Vivamus nec neque a libero sodales aliquam a et dolor.</li><li>Vestibulum rhoncus sagittis dolor vel finibus.</li><li>Integer feugiat lacus ut efficitur mattis. Sed quis molestie velit.</li></ul><h2>02. Limitations</h2><p>Praesent placerat dictum elementum. Nam pulvinar urna vel lectus maximus, eget faucibus turpis hendrerit. Sed iaculis molestie arcu, et accumsan nisi. Quisque molestie velit vitae ligula luctus bibendum. Duis sit amet eros mollis, viverra ipsum sed, convallis sapien. Donec justo erat, pulvinar vitae dui ut, finibus euismod enim. Donec velit tortor, mollis eu tortor euismod, gravida lacinia arcu.</p><ul><li>In ac turpis mi. Donec quis semper neque. Nulla cursus gravida interdum.</li><li>Curabitur luctus sapien augue.</li><li>mattis faucibus nisl vehicula nec, Mauris at scelerisque lorem.</li><li>Nullam tempus felis ipsum, sagittis malesuada nulla vulputate et. Aenean vel metus leo.</li><li>Vivamus nec neque a libero sodales aliquam a et dolor.</li></ul><h2>03. Security</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ex neque, elementum eu blandit in, ornare eu purus. Fusce eu rhoncus mi, quis ultrices lacus. Phasellus id pellentesque nulla. Cras erat nisi, mattis et efficitur et, iaculis a lacus. Fusce gravida augue quis leo facilisis.</p><h2>04. Privacy Policy</h2><p>Praesent non sem facilisis, hendrerit nisi vitae, volutpat quam. Aliquam metus mauris, semper eu eros vitae, blandit tristique metus. Vestibulum maximus nec justo sed maximus. Vivamus sit amet turpis sem. Integer vitae tortor ac ex scelerisque facilisis ac vitae urna. In hac habitasse platea dictumst. Maecenas imperdiet tortor arcu, nec tincidunt neque malesuada volutpat.</p><ul><li>In ac turpis mi. Donec quis semper neque. Nulla cursus gravida interdum.</li><li>Mauris at scelerisque lorem. Nullam tempus felis ipsum, sagittis malesuada nulla vulputate et.</li><li>Aenean vel metus leo.</li><li>Vestibulum rhoncus sagittis dolor vel finibus.</li><li>Integer feugiat lacus ut efficitur mattis. Sed quis molestie velit.</li></ul><p>Fusce rutrum mauris sit amet justo rutrum, ut sodales lorem ullamcorper. Aliquam vitae iaculis urna. Nulla vitae mi vel nisl viverra ullamcorper vel elementum est. Mauris vitae elit nec enim tincidunt aliquet. Donec ultrices nulla a enim pulvinar, quis pulvinar lacus consectetur. Donec dignissim, risus nec mollis efficitur, turpis erat blandit urna, eget elementum lacus lectus eget lorem.</p><p><br>&nbsp;</p>';
        $cms->maintenance_image = 'frontend/assets/images/all-img/coming-banner.png';

        $cms->save();
    }
}
