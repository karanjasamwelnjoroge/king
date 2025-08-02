<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /page/login.php');
    exit();
}
require_once '../../config/database.php';
// Fetch categories
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$categories = [];
$res = $conn->query("SELECT category_id, name FROM job_categories ORDER BY name");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $categories[] = $row;
    }
}
$conn->close();

// Define all questions for the questionnaire
$questions = [
    1 => [
        'text' => 'How often do you save leftover food?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Once a day']
    ],
    2 => [
        'text' => 'How often do you compact your trash?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Once a day']
    ],
    3 => [
        'text' => 'About how many minutes is your average shower?',
        'options' => ['5 minutes', '7 minutes', '13 minutes', '22 minutes', '36 minutes']
    ],
    4 => [
        'text' => 'How often do you use disposable plates or cups at home?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Once a day']
    ],
    5 => [
        'text' => 'About how many microwavable dinners do you have in your freezer?',
        'options' => ['Zero', 'One', '2 to 4', '5 to 8', '9+']
    ],
    6 => [
        'text' => 'How much toilet paper do you use?',
        'options' => ['None', 'Tiny: 2-3 feet', 'Moderate: 4 - 6 feet', 'Lots: 6 – 9 feet', 'Tons']
    ],
    7 => [
        'text' => 'How much of your own food do you grow?',
        'options' => ['None', 'One plant type', 'Two plant types', '3-5 plant types', 'More than 5']
    ],
    8 => [
        'text' => 'Do you have solar panels?',
        'options' => ['No, and I have no interest.', 'No, but I would like to.', 'No, but I\'m saving money to get them.', 'I\'m looking for a vendor to purchase them.', 'I have them.']
    ],
    9 => [
        'text' => 'Do you have dual-pane windows?',
        'options' => ['No, and I have no interest.', 'No, but I would like to.', 'No, but I\'m saving money to get them.', 'I\'m looking for a vendor to purchase them.', 'I have them.']
    ],
    10 => [
        'text' => 'How long have you had a savings account?',
        'options' => ['Never', '1 year or less', '1-5 years', '6-10 years', 'Entire life']
    ],
    11 => [
        'text' => 'How much credit card debt do you have?',
        'options' => ['$1000 or less', '$1000 - $5000', '$5,000 to $15,000', 'Over $15,000', 'No credit cards']
    ],
    12 => [
        'text' => 'How much is your car loan?',
        'options' => ['$1000 or less', '$1000 - $5000', '$5,000 to $15,000', 'Over $15,000', 'No car loans']
    ],
    13 => [
        'text' => 'How often do you go to the dentist?',
        'options' => ['Twice/yearly', 'Once/yearly', 'Once every other year', 'Rarely, but not never', 'Never']
    ],
    14 => [
        'text' => 'How often do you get an oil change for your car?',
        'options' => ['No car', '3 months', '4 months', 'Twice a year', 'Once a year']
    ],
    15 => [
        'text' => 'When you do your laundry, how many loads of laundry do you do on that day?',
        'options' => ['1 or 2', '3 or 4', '5 to 6', '7 to 9 loads.', '10+ lots.']
    ],
    16 => [
        'text' => 'How often do you shop at thrift stores?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Once a day']
    ],
    17 => [
        'text' => 'How often do you attempt to solve your own plumbing, appliance repair, or pest control problems?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Once a day']
    ],
    18 => [
        'text' => 'How many times in the last 15 years have you helped a stray or wild animal?',
        'options' => ['Never', 'Once or twice', '3 to 4 times', '5 to 7 times', '8 or more']
    ],
    19 => [
        'text' => 'How many times in the last 15 years have you helped a stranger?',
        'options' => ['Never', 'Once or twice', '3 to 4 times', '5 to 7 times', '8 or more']
    ],
    20 => [
        'text' => 'What would you do to protect the life of your child?',
        'options' => ['I don\'t have children.', 'I would call the police.', 'I would beat someone up- then call the police.', 'I would inflict great bodily injury if required.', 'I would kill if required.']
    ],
    21 => [
        'text' => 'Where do you think black holes go?',
        'options' => ['Nowhere, they are not holes.', 'It is a wormhole to another part of the galaxy.', 'To a parallel universe.', 'To a different time.', 'To God.']
    ],
    22 => [
        'text' => 'Is it possible to hear a bullet and dodge it?',
        'options' => ['No.', 'Yes, but you would need supper human speed.', 'Yes, but you would need really good hearing and be able to move over 88 MPH.', 'Yes, all you need is really good hearing.', 'Yes.']
    ],
    23 => [
        'text' => 'Do you believe faster than light travel is possible for a 4-man vehicle?',
        'options' => ['No, it requires too much energy.', 'Yes, but it would require all the power of 10 nuclear power plants to do it.', 'Yes, but you need to convert several grams of mater to pure energy to do it.', 'Yes, but you need to convert several tons of mater to pure energy to do it.', 'Yes, but you need to convert several planets to pure energy to do it.']
    ],
    24 => [
        'text' => 'How many people live on earth who have supernatural powers like the X-men, Superman, etc. (even if in secret)?',
        'options' => ['Zero', 'Less than 20', '20 to 100', '100 to 1,000', 'More than 1,000']
    ],
    25 => [
        'text' => 'How many aliens (who have traveled from other worlds in the universe to ours) live on earth?',
        'options' => ['Zero', 'Less than 20', '20 to 100', '100 to 1,000', 'More than 1,000']
    ],
    26 => [
        'text' => 'How many undead daemons from the underworld live on earth?',
        'options' => ['Zero', 'Less than 20', '20 to 100', '100 to 1,000', 'More than 1,000']
    ],
    27 => [
        'text' => 'How many people on earth can use witchcraft magic with spells like: teleportation, invisibility, resurrection, and summoning fire, lightning, objects, or beasts from thin air?',
        'options' => ['Zero', 'Less than 20', '20 to 100', '100 to 1,000', 'More than 1,000']
    ],
    28 => [
        'text' => 'Do you believe it is possible to travel back in time and change the past?',
        'options' => ['No.', 'Yes, but can\'t run into yourself or you vanish from the timeline.', 'Yes, but you would need to be able to move over 88 MPH.', 'Yes, but you would inhabit your own body and only bring your memories.', 'Yes, people do it frequently.']
    ],
    29 => [
        'text' => 'How much of the Theory of Evolution do you believe?',
        'options' => ['None of it.', 'Life is about 15,000 years old.', 'Life is about 60,000 years old and started with the Neanderthals.', 'Life started several million years ago, but we did not evolve from apes.', 'We evolved from a single cell in the primordial goo about 4 billion years ago.']
    ],
    30 => [
        'text' => 'How often do you watch TV?',
        'options' => ['Never', 'Once a month', 'Once a week', 'Every day', 'All day long.']
    ],
    31 => [
        'text' => 'How often do you watch horror?',
        'options' => ['Never', 'Once a month', 'Once a week', 'Every day', 'All day long.']
    ],
    32 => [
        'text' => 'How often do you watch pornography?',
        'options' => ['Never', 'Once a month', 'Once a week', 'Every day', 'All day long.']
    ],
    33 => [
        'text' => 'How often do you watch science?',
        'options' => ['Never', 'Once a month', 'Once a week', 'Every day', 'All day long.']
    ],
    34 => [
        'text' => 'How often do you yell or scream?',
        'options' => ['Never', 'Once a month', 'Once a week', 'Every day', 'All day long.']
    ],
    35 => [
        'text' => 'How often do you get into a physical fist fight?',
        'options' => ['Never', 'Once a decade', 'Once a year', 'Once a month', 'Often']
    ],
    36 => [
        'text' => 'How often do you use marijuana?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Every day.']
    ],
    37 => [
        'text' => 'How often do you use drugs other than marijuana?',
        'options' => ['Never', 'Once a month', 'Once a week', 'Every day', 'All day long.']
    ],
    38 => [
        'text' => 'How often do get drunk?',
        'options' => ['Never', 'Once a month', 'Once a week', 'Every day', 'All day long.']
    ],
    39 => [
        'text' => 'How often do you drink alcohol?',
        'options' => ['Never', 'Once a month', 'Once a week', 'Every day', 'All day long.']
    ],
    40 => [
        'text' => 'How often do you smoke tobacco?',
        'options' => ['Never', 'Once a month', 'Once a week', 'Every day', 'All day long.']
    ],
    41 => [
        'text' => 'How often do you vape?',
        'options' => ['Never', 'Once a month', 'Once a week', 'Every day', 'All day long.']
    ],
    42 => [
        'text' => 'Of the last 15 years, how many of those years did you have a pet dog, cat, pig, hamster, bird, or similar animal that you played with?',
        'options' => ['All 15', '10 to 14', '4 to 9', '1 to 3', 'None']
    ],
    43 => [
        'text' => 'How many tattoos do you have?',
        'options' => ['None', '1 or 2', '3 to 5', '6 to 9', 'Lots']
    ],
    44 => [
        'text' => 'How many body piercings do you have?',
        'options' => ['None', '1 or 2', '3 to 5', '6 to 9', 'Lots']
    ],
    45 => [
        'text' => 'How often do you go to strip clubs?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Every day']
    ],
    46 => [
        'text' => 'How often do you go to sports bars?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Every day']
    ],
    47 => [
        'text' => 'How often do you go to bars without a dance floor?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Every day']
    ],
    48 => [
        'text' => 'How often do you gamble?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Every day']
    ],
    49 => [
        'text' => 'How often do you go to casinos?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Every day']
    ],
    50 => [
        'text' => 'How often do hire a prostitute?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Every day']
    ],
    51 => [
        'text' => 'How often do you play the Lottery?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Every day']
    ],
    52 => [
        'text' => 'How often do you get any kind of speeding ticket?',
        'options' => ['Never', '1 in 15 years', '2 in 10 years', 'Once a year', 'Once a month']
    ],
    53 => [
        'text' => 'How often do you go out to fast food?',
        'options' => ['Never', 'Once a year', 'Once a month', 'Once a week', 'Every day']
    ],
    54 => [
        'text' => 'Describe your relationship with your parents?',
        'options' => ['I don\'t talk to them; they don\'t talk to me.', 'I\'m better off without them. Once or twice a year I suffer their company.', 'It\'s okay. They try to run my life, but they don\'t understand me.', 'I love them, but things could be better. I love them. I am blessed.', 'They help me and support me. I would walk through fire for them.']
    ],
    55 => [
        'text' => 'Describe your relationship with your children?',
        'options' => ['I don\'t have children.', 'I don\'t talk to them; they don\'t talk to me.', 'It\'s okay. They try to run my life, but they don\'t understand me.', 'I love them, but things could be better.', 'They help me and support me. I would walk through fire for them.']
    ],
    56 => [
        'text' => 'Describe your relationship with your friends?',
        'options' => ['I don\'t have friends.', 'I don\'t talk to them; they don\'t talk to me.', 'It\'s okay. They try to run my life, but they don\'t understand me.', 'I love them, but things could be better.', 'They help me and support me. I would walk through fire for them.']
    ]
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Resume | CareerConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary: #2563eb;
        --primary-light: #3b82f6;
        --primary-dark: #1d4ed8;
        --secondary: #64748b;
        --accent: #06b6d4;
        --success: #10b981;
        --warning: #f59e0b;
        --error: #ef4444;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --gray-900: #0f172a;
        --white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        --radius: 0.5rem;
        --radius-lg: 0.75rem;
        --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
        background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
        color: var(--gray-900);
        line-height: 1.6;
        min-height: 100vh;
        padding: 1rem 0;
    }

    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .page-header {
        text-align: center;
        margin-bottom: 2rem;
        padding: 1.5rem 0;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        letter-spacing: -0.025em;
    }

    .page-subtitle {
        font-size: 1.125rem;
        color: var(--gray-600);
        max-width: 600px;
        margin: 0 auto;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 2rem;
        padding: 0.5rem 1rem;
        border-radius: var(--radius);
        transition: var(--transition);
        background: var(--white);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }

    .back-link:hover {
        background: var(--gray-50);
        box-shadow: var(--shadow);
        transform: translateY(-1px);
    }

    .back-link i {
        margin-right: 0.5rem;
        font-size: 0.875rem;
    }

    .form-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--gray-200);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .form-section {
        padding: 2rem;
        border-bottom: 1px solid var(--gray-100);
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--gray-100);
    }

    .section-icon {
        width: 2.5rem;
        height: 2.5rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        margin-right: 1rem;
        font-size: 1.125rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
        letter-spacing: -0.025em;
    }

    .section-description {
        color: var(--gray-600);
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        letter-spacing: 0.025em;
    }

    .required::after {
        content: '*';
        color: var(--error);
        margin-left: 0.25rem;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.875rem;
        transition: var(--transition);
        background: var(--white);
        color: var(--gray-900);
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
    }

    .form-textarea {
        min-height: 120px;
        resize: vertical;
        font-family: inherit;
    }

    .file-upload-area {
        border: 2px dashed var(--gray-300);
        border-radius: var(--radius-lg);
        padding: 2rem;
        text-align: center;
        background: var(--gray-50);
        transition: var(--transition);
        cursor: pointer;
        position: relative;
    }

    .file-upload-area:hover {
        border-color: var(--primary);
        background: rgb(37 99 235 / 0.05);
    }

    .file-upload-area.dragover {
        border-color: var(--primary);
        background: rgb(37 99 235 / 0.1);
    }

    .file-upload-icon {
        width: 3rem;
        height: 3rem;
        background: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        margin: 0 auto 1rem;
        font-size: 1.25rem;
    }

    .file-upload-text {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .file-upload-hint {
        font-size: 0.75rem;
        color: var(--gray-500);
    }

    .file-input {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .file-selected {
        margin-top: 1rem;
        padding: 0.75rem 1rem;
        background: var(--success);
        color: var(--white);
        border-radius: var(--radius);
        font-size: 0.875rem;
        font-weight: 500;
        display: none;
    }

    .questionnaire-container {
        background: var(--gray-50);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        max-height: 70vh;
        overflow-y: auto;
        border: 1px solid var(--gray-200);
    }

    .question-card {
        background: var(--white);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        transition: var(--transition);
    }

    .question-card:hover {
        box-shadow: var(--shadow);
    }

    .question-number {
        display: inline-block;
        background: var(--primary);
        color: var(--white);
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
    }

    .question-text {
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
    }

    .option-item {
        position: relative;
    }

    .option-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .option-label {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        background: var(--white);
        cursor: pointer;
        transition: var(--transition);
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--gray-700);
        word-break: break-word;
    }

    .option-label:hover {
        border-color: var(--primary-light);
        background: rgb(37 99 235 / 0.05);
    }

    .option-input:checked + .option-label {
        border-color: var(--primary);
        background: var(--primary);
        color: var(--white);
    }

    .option-indicator {
        width: 1rem;
        height: 1rem;
        border: 2px solid currentColor;
        border-radius: 50%;
        margin-right: 0.75rem;
        position: relative;
        flex-shrink: 0;
    }

    .option-input:checked + .option-label .option-indicator::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0.375rem;
        height: 0.375rem;
        background: currentColor;
        border-radius: 50%;
        transform: translate(-50%, -50%);
    }

    .submit-section {
        position: sticky;
        bottom: 0;
        background: var(--white);
        border-top: 1px solid var(--gray-200);
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: center;
        gap: 1rem;
        box-shadow: 0 -4px 6px -1px rgb(0 0 0 / 0.1);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: var(--radius);
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: var(--transition);
        min-width: 120px;
        gap: 0.5rem;
    }

    .btn-primary {
        background: var(--primary);
        color: var(--white);
        box-shadow: var(--shadow);
    }

    .btn-primary:hover:not(:disabled) {
        background: var(--primary-dark);
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: var(--white);
        color: var(--gray-700);
        border: 2px solid var(--gray-300);
    }

    .btn-secondary:hover {
        background: var(--gray-50);
        border-color: var(--gray-400);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    .spinner {
        width: 1rem;
        height: 1rem;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: var(--radius);
        margin-bottom: 1.5rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid;
    }

    .alert-success {
        background: rgb(16 185 129 / 0.1);
        color: var(--success);
        border-color: rgb(16 185 129 / 0.3);
    }

    .alert-error {
        background: rgb(239 68 68 / 0.1);
        color: var(--error);
        border-color: rgb(239 68 68 / 0.3);
    }

    .progress-bar {
        height: 0.25rem;
        background: var(--gray-200);
        border-radius: 9999px;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        border-radius: 9999px;
        transition: width 0.3s ease;
        width: 0%;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 0 0.75rem;
        }

        .page-title {
            font-size: 1.75rem;
        }

        .page-subtitle {
            font-size: 1rem;
        }

        .form-section {
            padding: 1.5rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .options-grid {
            grid-template-columns: 1fr;
        }

        .section-header {
            flex-direction: column;
            text-align: center;
        }

        .section-icon {
            margin: 0 0 1rem 0;
        }

        .submit-section {
            padding: 1rem;
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .questionnaire-container {
            max-height: 60vh;
        }
    }

    @media (max-width: 480px) {
        body {
            padding: 0.5rem 0;
        }

        .page-header {
            margin-bottom: 1.5rem;
            padding: 1rem 0;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .form-section {
            padding: 1rem;
        }

        .file-upload-area {
            padding: 1.5rem;
        }

        .question-card {
            padding: 1rem;
        }
    }

    /* Custom scrollbar */
    .questionnaire-container::-webkit-scrollbar {
        width: 0.5rem;
    }

    .questionnaire-container::-webkit-scrollbar-track {
        background: var(--gray-100);
        border-radius: var(--radius);
    }

    .questionnaire-container::-webkit-scrollbar-thumb {
        background: var(--gray-400);
        border-radius: var(--radius);
    }

    .questionnaire-container::-webkit-scrollbar-thumb:hover {
        background: var(--gray-500);
    }

    /* Animation for form sections */
    .form-card {
        animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div id="formAlert"></div>

        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>

        <div class="page-header">
            <h1 class="page-title">Create Professional Resume</h1>
            <p class="page-subtitle">Build your standout resume and complete our comprehensive questionnaire to unlock the best career opportunities</p>
        </div>

        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <form id="resumeForm" enctype="multipart/form-data" autocomplete="off">
            <div class="form-card">
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Resume Information</h2>
                            <p class="section-description">Provide your basic resume details and upload your CV</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="resume_name" class="form-label required">Resume Name</label>
                            <input type="text" name="resume_name" id="resume_name" class="form-input" required
                                placeholder="e.g. Software Engineer Resume 2024">
                        </div>
                        <div class="form-group">
                            <label for="categorySelect" class="form-label required">Job Category</label>
                            <select name="category_id" id="categorySelect" class="form-select" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['category_id']) ?>">
                                    <?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bio" class="form-label required">Professional Summary</label>
                        <textarea name="bio" id="bio" class="form-textarea" required
                            placeholder="Describe your professional background, key skills, and notable achievements. This summary will help employers understand your value proposition."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Upload Your CV</label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="file-upload-text">Click to upload or drag and drop</div>
                            <div class="file-upload-hint">PDF files only, max 5MB</div>
                            <input type="file" name="cv_file" id="cv_file" class="file-input" accept="application/pdf" required>
                        </div>
                        <div class="file-selected" id="fileSelected">
                            <i class="fas fa-check-circle"></i>
                            <span id="fileName"></span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Employment Questionnaire</h2>
                            <p class="section-description">Complete this comprehensive assessment to help us match you with the perfect opportunities</p>
                        </div>
                    </div>

                    <div class="questionnaire-container" id="questionnaire">
                        <?php foreach ($questions as $qNum => $question): ?>
                        <div class="question-card">
                            <div class="question-number"><?= $qNum ?></div>
                            <div class="question-text"><?= htmlspecialchars($question['text']) ?></div>
                            <div class="options-grid">
                                <?php foreach ($question['options'] as $index => $option): ?>
                                <?php $optionValue = chr(65 + $index); // A, B, C, D, E ?>
                                <div class="option-item">
                                    <input type="radio" name="q<?= $qNum ?>" value="<?= $optionValue ?>" 
                                           id="q<?= $qNum ?><?= strtolower($optionValue) ?>" class="option-input" required>
                                    <label for="q<?= $qNum ?><?= strtolower($optionValue) ?>" class="option-label">
                                        <div class="option-indicator"></div>
                                        <?= htmlspecialchars($option) ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="submit-section">
                    <button type="button" class="btn btn-secondary" id="saveDraftBtn">
                        <i class="fas fa-save"></i>
                        Save Draft
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitText">
                            <i class="fas fa-check"></i>
                            Create Resume
                        </span>
                        <span id="submitSpinner" class="spinner" style="display:none;"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Progress tracking
        const progressFill = document.getElementById('progressFill');
        const form = document.getElementById('resumeForm');
        const totalQuestions = <?= count($questions) ?>;
        
        function updateProgress() {
            const answeredQuestions = form.querySelectorAll('input[type="radio"]:checked').length;
            const resumeFields = form.querySelectorAll('#resume_name, #categorySelect, #bio, #cv_file');
            let filledFields = 0;
            
            resumeFields.forEach(field => {
                if (field.value.trim() !== '') filledFields++;
            });
            
            const totalFields = resumeFields.length + totalQuestions;
            const completedFields = filledFields + answeredQuestions;
            const progress = (completedFields / totalFields) * 100;
            
            progressFill.style.width = progress + '%';
        }

        // File upload handling
        const fileInput = document.getElementById('cv_file');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileSelected = document.getElementById('fileSelected');
        const fileName = document.getElementById('fileName');

        fileInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                const file = this.files[0];
                fileName.textContent = file.name;
                fileSelected.style.display = 'flex';
                fileUploadArea.style.border = '2px solid var(--success)';
                fileUploadArea.style.background = 'rgb(16 185 129 / 0.05)';
                updateProgress();
            }
        });

        // Drag and drop functionality
        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        fileUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        fileUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type === 'application/pdf') {
                fileInput.files = files;
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            }
        });

        // Form field change tracking
        form.addEventListener('change', updateProgress);
        form.addEventListener('input', updateProgress);

        // Form submission
        const formAlert = document.getElementById('formAlert');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitSpinner = document.getElementById('submitSpinner');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Clear previous alerts
            formAlert.innerHTML = '';

            // Show loading state
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            submitSpinner.style.display = 'inline-block';

            // Prepare form data
            const formData = new FormData(form);

            // Submit form via AJAX
            fetch('php/resume/submit_resume.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline-flex';
                    submitSpinner.style.display = 'none';

                    if (data.success) {
                        // Show success message
                        formAlert.innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            ${data.message}
                        </div>
                    `;

                        // Reset form
                        form.reset();
                        fileSelected.style.display = 'none';
                        fileUploadArea.style.border = '2px dashed var(--gray-300)';
                        fileUploadArea.style.background = 'var(--gray-50)';
                        updateProgress();

                        // Scroll to top
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    } else {
                        // Show error message
                        formAlert.innerHTML = `
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            ${data.error || 'Failed to save resume. Please try again.'}
                        </div>
                    `;

                        // Scroll to top
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                })
                .catch(error => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline-flex';
                    submitSpinner.style.display = 'none';

                    // Show error message
                    formAlert.innerHTML = `
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        An error occurred. Please try again.
                    </div>
                `;

                    console.error('Error:', error);
                });
        });

        // Save draft functionality
        const saveDraftBtn = document.getElementById('saveDraftBtn');
        saveDraftBtn.addEventListener('click', function() {
            // Implement save draft functionality here
            alert('Draft save functionality would be implemented here');
        });

        // Smooth scrolling for questionnaire
        const questionnaire = document.getElementById('questionnaire');
        questionnaire.addEventListener('scroll', function() {
            const isScrolled = this.scrollTop > 0;
            this.style.boxShadow = isScrolled ? 
                'inset 0 4px 6px -1px rgb(0 0 0 / 0.1)' : 
                'var(--shadow-sm)';
        });

        // Initialize progress
        updateProgress();
    });
    </script>
</body>

</html>