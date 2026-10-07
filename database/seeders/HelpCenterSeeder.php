<?php

namespace Database\Seeders;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\HelpFaq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HelpCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Getting started',
                'slug' => 'getting-started',
                'description' => 'Learn the essentials for your first research record.',
                'icon' => 'book-open',
                'articles' => [
                    ['Creating your research record', 'Start a record from Research & Documents, enter the required title and academic details, and save it as a draft. Check the details before sharing or submitting it for review.'],
                    ['Understanding your workspace', 'The dashboard brings together research activity and the shortcuts available to your assigned role. Use the main navigation to open research records, workflow, analytics, profile settings, and help.'],
                    ['Setting up your profile', 'Open Settings & Profile to review your name, email address, workplace, and profile photo. Your assigned role is managed by an administrator and cannot be changed from personal settings.'],
                    ['Adding collaborators', 'Research records are associated with the student and academic staff configured by an administrator or coordinator. If a collaborator is missing or incorrect, contact your coordinator rather than sharing another user’s account.'],
                    ['Understanding research statuses', 'A status describes the current stage of a record, such as Draft, Submitted, Under Review, Revision Required, Approved, or Archived. Open the record history for the changes and decisions recorded so far.'],
                    ['Navigating the dashboard', 'Use the dashboard cards to review your role-relevant research summary and recent activity. Choose a record or navigation destination to see its complete details and available actions.'],
                ],
            ],
            [
                'name' => 'Research & documents',
                'slug' => 'research-documents',
                'description' => 'Upload, organize, and manage document versions.',
                'icon' => 'file-text',
                'articles' => [
                    ['Uploading research documents', 'Open the research record and use its Documents area to select the file for that stage. Confirm the document title and file before submitting; the system records the upload against the selected research record.'],
                    ['Supported file types', 'The upload form lists the file types and size limits accepted for that document. If a file is rejected, check the format and size, export it to a supported type, and try again.'],
                    ['Understanding document versions', 'A new version keeps a record of a later file without silently replacing the previous submission. Open the document history to compare version labels, upload dates, and the users who added them.'],
                    ['Replacing a research document', 'Add a new document version from the research record when a correction is needed. Explain the change in the version details and follow the workflow instructions if the record has already been submitted for review.'],
                    ['Viewing document history', 'Open Documents on a research record to review its stored files and version history. Each entry is associated with its record so reviewers can refer to the correct submitted material.'],
                    ['Downloading research files', 'Use the download action beside a document version you are authorized to view. If access is denied or the download is unavailable, confirm that you are signed in to the correct account and contact the record coordinator.'],
                    ['Organizing research documents', 'Keep documents attached to the correct research record and use descriptive titles that identify their stage or purpose. Avoid uploading unrelated files or creating duplicate versions without explaining the update.'],
                    ['Document permissions', 'Access to research files follows the record’s role and assignment rules. If a reviewer or collaborator needs access, ask an administrator or coordinator to confirm the record assignments.'],
                ],
            ],
            [
                'name' => 'Workflow & approvals',
                'slug' => 'workflow-approvals',
                'description' => 'Understand reviews, revisions, and decisions.',
                'icon' => 'workflow',
                'articles' => [
                    ['Submission workflow', 'A researcher prepares a record and submits it for review. The record then follows its configured adviser, panel, and coordination steps; the activity history records workflow actions.'],
                    ['Preparing for adviser review', 'Before submitting, confirm that the research details and required document version are complete. An adviser reviews the material assigned to them and records feedback or a decision through the research record.'],
                    ['Understanding panel review', 'Panel members review records assigned to them and provide evaluations or comments through the review action available for the record. Check the reviewer instructions and due dates provided by your coordinator.'],
                    ['How approval works', 'Approval is recorded when the authorized reviewer or coordinator completes the applicable workflow step. Review the recorded decision and history; a status change does not replace any separate institutional requirements.'],
                    ['Requesting a revision', 'When review feedback requests changes, open the research record and read each comment before preparing a corrected document version. Contact the reviewer or coordinator if a comment needs clarification.'],
                    ['Resubmitting revised work', 'Upload the corrected file as a new version and follow the resubmission action shown for the record. Include a concise response to the requested changes when the workflow provides a place for it.'],
                    ['Final approval and archival', 'After required reviews are complete, the authorized staff or coordinator records the final decision. Completed records may be moved to the archive, where they remain available according to your access permissions.'],
                ],
            ],
            [
                'name' => 'Users & roles',
                'slug' => 'users-roles',
                'description' => 'Understand account access and role responsibilities.',
                'icon' => 'users',
                'articles' => [
                    ['Managing user accounts', 'Administrators can review user accounts in User Management and update account details using the available controls. Verify the person and institutional information before saving a change.'],
                    ['Researcher and student access', 'Student accounts can work with research records associated with them and see the actions enabled for their role. Ask an administrator to correct an assignment or access issue.'],
                    ['Adviser permissions', 'Faculty advisers use the workspace to access assigned research and record review feedback or decisions. Their access is based on configured assignments and does not imply access to every record.'],
                    ['Panel member permissions', 'Panel members can review records assigned to them and provide evaluation feedback through the available workflow actions. Contact a coordinator if an expected assignment is missing.'],
                    ['Administrator permissions', 'Administrators manage user accounts, assigned roles, and system-level workspace functions. Assign only the access needed for a person’s institutional responsibilities.'],
                ],
            ],
            [
                'name' => 'Analytics',
                'slug' => 'analytics',
                'description' => 'Read research activity and approval performance summaries.',
                'icon' => 'chart',
                'articles' => [
                    ['Research statistics', 'The Analytics area summarizes research records available to your account. Review the date range and status definitions before comparing totals.'],
                    ['Understanding approval metrics', 'Approval metrics describe recorded workflow outcomes for the selected records and period. They are operational summaries and should be interpreted alongside the underlying record history.'],
                    ['Review performance', 'Review activity summaries show recorded review progress for assigned research. Use a record’s history for the full context behind an aggregate count.'],
                    ['Activity reports', 'Activity reports help authorized users see recorded changes and workflow events. Use filters available in the analytics screen to focus on a relevant group or period.'],
                ],
            ],
            [
                'name' => 'Account & settings',
                'slug' => 'account-settings',
                'description' => 'Keep your profile and account access current.',
                'icon' => 'settings',
                'articles' => [
                    ['Editing your profile', 'Open Settings & Profile, choose Edit profile, update your name, email, or workplace, and save. If you change your email address, you may need to verify the new address before using verified features.'],
                    ['Changing your password', 'Use the password section in Settings & Profile and provide the current password when requested. Choose a strong password that you do not reuse for another service.'],
                    ['Managing notifications', 'Review notification options in Settings & Profile to understand which updates are available. If you are expecting a workflow update, check the research activity history as the authoritative record.'],
                    ['Adding or changing your profile photo', 'Choose a JPG, PNG, or WebP photo no larger than the limit shown in Profile Settings. Preview the selected photo, then save your profile to upload it; use Remove to clear the current photo.'],
                    ['Reviewing account preferences', 'Settings & Profile contains the account and interface preferences currently provided by CCTAS. Preferences that are not shown in the interface are not configurable from this page.'],
                ],
            ],
            [
                'name' => 'Security & privacy',
                'slug' => 'security-privacy',
                'description' => 'Protect your account and research information.',
                'icon' => 'shield',
                'articles' => [
                    ['Protecting your account', 'Use your own account, keep your password private, and sign out on shared devices. Contact an administrator promptly if you notice unfamiliar access or account changes.'],
                    ['Research record access', 'CCTAS applies role and assignment checks to research actions. If you believe someone can access a record incorrectly, report the record and account details to an administrator.'],
                    ['Privacy and personal information', 'Only enter information needed for the research workflow and use the designated fields for project information. Do not include passwords, access tokens, or unrelated sensitive personal data in help requests.'],
                    ['Handling research documents securely', 'Upload research materials only to the intended record and follow your institution’s document-handling rules. Avoid sending confidential files through unrelated channels.'],
                ],
            ],
            [
                'name' => 'Troubleshooting',
                'slug' => 'troubleshooting',
                'description' => 'Resolve common workspace questions quickly.',
                'icon' => 'tool',
                'articles' => [
                    ['Trouble signing in', 'Confirm that you are using the email address associated with your account and that your account is verified when required. Use the password reset flow if needed; contact support if you still cannot access the account.'],
                    ['A document upload failed', 'Check the accepted file type and size shown by the upload form, then retry with a stable connection. If the problem continues, report the record, file type, and error message without attaching confidential credentials.'],
                    ['I did not receive a notification', 'Check your account email and the research record’s activity history for the latest update. If the expected action is missing, contact the assigned reviewer or submit a support request with the record reference.'],
                    ['I cannot access a research record', 'Confirm that you are signed in with the expected account and that the record is assigned to your role. Ask a coordinator or administrator to review the assignment instead of requesting another user’s credentials.'],
                    ['A workflow action is unavailable', 'Actions depend on the record status and your role. Review the current status and assigned reviewers; contact the coordinator if the action should be available but is not displayed.'],
                    ['Reporting a system problem', 'Use Report an Issue in the Help Center and include the page, steps to reproduce, and a short description. Do not include passwords or confidential information in a screenshot.'],
                ],
            ],
        ];

        foreach ($categories as $index => $categoryData) {
            $topics = $categoryData['articles'];
            unset($categoryData['articles']);

            $category = HelpCategory::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                [...$categoryData, 'sort_order' => $index + 1],
            );

            foreach ($topics as [$title, $excerpt]) {
                $slug = Str::slug($title);
                $instructions = match ($category->slug) {
                    'getting-started' => '1. Sign in to CCTAS and review your role-specific dashboard.\n2. Open the relevant navigation area and read the record details before making changes.\n3. Save the information and confirm the resulting status or activity history.',
                    'research-documents' => '1. Open the intended research record and verify its title and owner.\n2. Use Documents to add the supported file or create a new version; do not replace an earlier submission without recording the change.\n3. Confirm the uploaded file appears on the record and follow its current workflow status.',
                    'workflow-approvals' => '1. Open the research record and confirm its current status and assigned reviewer.\n2. Read the recorded feedback and use the workflow action available to your role.\n3. Check the activity history to confirm the decision, revision, or next assigned step.',
                    'users-roles' => '1. An administrator opens User Management and locates the correct account.\n2. Verify the person’s account details and institutional responsibilities before changing assignments or access.\n3. Save the update and confirm that the account reflects the intended role.',
                    'analytics' => '1. Open Analytics and select the relevant reporting period or available filter.\n2. Read the definitions and scope of the displayed totals before comparing records.\n3. Open the underlying research record when you need context for an aggregate metric.',
                    'account-settings' => '1. Open Settings & Profile from the workspace navigation.\n2. Update the available personal details or security setting and review the validation messages.\n3. Save the change and confirm it is displayed; contact an administrator for role changes.',
                    'security-privacy' => '1. Use your own CCTAS account and keep its credentials private.\n2. Share research information only through its authorized record and workflow.\n3. Report unexpected access or suspicious account activity to an administrator.',
                    'troubleshooting' => '1. Note the exact page, action, and message where the issue occurred.\n2. Check the record status, your account role, and any file requirements relevant to the action.\n3. Retry once; if the issue remains, submit a support request with the record reference and reproduction steps.',
                    default => '1. Sign in to CCTAS and open the relevant workspace area.\n2. Review the record details and choose only an action available to your assigned role.\n3. Save the change and confirm the resulting status or activity history.',
                };
                $content = "Overview\n{$excerpt}\n\nRecommended steps\n{$instructions}\n\nIf you need more help\nContact your coordinator or use the Help Center support form. Include the relevant record reference and the exact message you see, but never share your password.";

                HelpArticle::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'help_category_id' => $category->id,
                        'title' => $title,
                        'excerpt' => $excerpt,
                        'content' => $content,
                        'reading_time' => max(2, (int) ceil(str_word_count($content) / 180)),
                        'is_published' => true,
                        'published_at' => now(),
                    ],
                );
            }
        }

        $faqs = [
            ['How do I submit a research document?', 'Open the research workspace and select the correct project. Open its Documents area, choose the required file, review its details, and use the submission action available for your current role. Confirm the resulting status and activity history before leaving the record.'],
            ['How does the approval workflow work?', 'A researcher prepares and submits a record, then assigned advisers and panel members review it and record feedback. Coordinators or other authorized staff complete the configured final steps. The record history shows the actual reviewers, actions, and dates; institutional requirements outside CCTAS still apply.'],
            ['How do I track my submission?', 'Open the research record or Workflow & Approvals to check its current status and recorded activity. The record displays available details, reviewers, comments, revision requests, and past decisions according to your permissions. Contact your coordinator if an expected assignment or update is missing.'],
            ['How can I request a revision?', 'Read the reviewer’s comments on the research record, prepare the requested changes, and upload the corrected file as a new document version. Use the available resubmission action and include a response to feedback when that field is provided. Ask the reviewer or coordinator when a requirement is unclear.'],
            ['How do I manage my account?', 'Open Settings & Profile to change your name, email address, workplace, and profile photo. Use the Security section to update your password and review the available preferences. An assigned role is controlled by a CCTAS administrator and cannot be edited from your own profile.'],
            ['How can administrators manage user roles?', 'Administrators can open User Management to create or update accounts and assign one of the system roles. Confirm a person’s identity and institutional responsibility before changing access. Users should not share accounts or attempt to grant themselves additional permissions.'],
        ];

        foreach ($faqs as $index => [$question, $answer]) {
            HelpFaq::query()->updateOrCreate(
                ['question' => $question],
                [
                    'answer' => $answer,
                    'sort_order' => $index + 1,
                    'is_published' => true,
                ],
            );
        }
    }
}
