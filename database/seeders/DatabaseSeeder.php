<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Blog\Database\Seeders\BlogDatabaseSeeder;
use Modules\Currency\Database\Seeders\CurrencyDatabaseSeeder;
use Modules\Faq\Database\Seeders\FaqCategorySeeder;
use Modules\Faq\Database\Seeders\FaqDatabaseSeeder;
use Modules\Language\Database\Seeders\LanguageDatabaseSeeder;
use Modules\Location\Database\Seeders\LocationDatabaseSeeder;
use Modules\Seo\Database\Seeders\SeoDatabaseSeeder;
use Modules\SetupGuide\Database\Seeders\SetupGuideDatabaseSeeder;
use Modules\Testimonial\Database\Seeders\TestimonialDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // For Packaging
        $this->packagingVersion();

        // For Development
        // $this->developmentVersion();
    }

    private function packagingVersion()
    {
        $this->call([
            SettingSeeder::class,
            LocationDatabaseSeeder::class,
            WebsiteSettingSeeder::class,
            CmsSeeder::class,
            SeoDatabaseSeeder::class,
            SetupGuideDatabaseSeeder::class,
            CookiesSeeder::class,
            MasterSeeder::class,
            ApplicationGroupSeeder::class,
            CmsContentSeeder::class,

            // Attribute
            ProfessionSeeder::class,
            JobTypeSeeder::class,
            JobCategorySeeder::class,
            JobCategorySlugSeeder::class,
            JobRoleSeeder::class,
            ExperienceSeeder::class,
            EducationSeeder::class,
            SalaryTypeSeeder::class,
            IndustryTypeSeeder::class,
            OrganizationTypeSeeder::class,
            TeamSizeSeeder::class,

            // Candidate Skills and Language
            SkillSeeder::class,
            CandidateSkillSeeder::class,
            CandidateLanguageSeeder::class,

            // jobs tags, benefits, skill
            TagSeeder::class,
            BenefitSeeder::class,
            // PageSeeder::class,
        ]);
    }

    private function developmentVersion()
    {

        $this->call([
            LocationDatabaseSeeder::class,
            ManualPaymentSeeder::class,

            // Setting
            SettingSeeder::class,
            WebsiteSettingSeeder::class,
            CmsSeeder::class,
            CurrencyDatabaseSeeder::class,
            SeoDatabaseSeeder::class,
            LanguageDatabaseSeeder::class,
            SetupGuideDatabaseSeeder::class,
            AdminSearchSeeder::class,
            CookiesSeeder::class,

            // Job Attributes
            EducationSeeder::class,
            ExperienceSeeder::class,
            JobTypeSeeder::class,
            JobRoleSeeder::class,
            SalaryTypeSeeder::class,
            TeamSizeSeeder::class,
            OrganizationTypeSeeder::class,
            ProfessionSeeder::class,
            IndustryTypeSeeder::class,
            JobCategorySeeder::class,
            JobCategorySlugSeeder::class,

            // Company, candidate and admin
            CompanySeeder::class,
            CandidateSeeder::class,
            AdminSeeder::class,

            // Candidate Cv
            CandidateResumeSeeder::class,

            // Jobs and bookmark
            JobSeeder::class,
            CandidateBookmarks::class,
            CandidateAppliedJobSeeder::class,
            CompanyBookmarks::class,

            // Company and candidate messenger
            MessengerUserSeeder::class,
            MessengerSeeder::class,

            // Others
            TestimonialDatabaseSeeder::class,
            BlogDatabaseSeeder::class,
            FaqCategorySeeder::class,
            FaqDatabaseSeeder::class,
            EarningSeeder::class,
            CmsContentSeeder::class,

            // Candidate Skills, Language, Experience and Education
            SkillSeeder::class,
            CandidateSkillSeeder::class,
            CandidateLanguageSeeder::class,
            CandidateExperienceSeeder::class,
            CandidateEducationSeeder::class,

            // jobs tags, benefits,skills
            TagSeeder::class,
            BenefitSeeder::class,
            JobTagSeeder::class,
            JobBenefitSeeder::class,
            JobSkillSeeder::class,

            PageSeeder::class,
            AdvertisementSeeder::class,
        ]);
    }
}
