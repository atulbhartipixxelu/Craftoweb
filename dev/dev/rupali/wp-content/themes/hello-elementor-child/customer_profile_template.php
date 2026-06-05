<?php
/**
 * Template Name: Customer Profile
 */

get_header();
$current_user = wp_get_current_user();
global $wpdb;

/* =======================
   FETCH DYNAMIC DATA
======================= */

$loan_table = $wpdb->prefix . 'loan_applications';
$complaint_table = $wpdb->prefix . 'user_complaints';

$user_email = $current_user->user_email;
$user_id = get_current_user_id();

// Loans
$loans = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $loan_table WHERE email = %s",
    $user_email
));

$total_loans = count($loans);

// Active Loans (approved)
$active_loans = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $loan_table WHERE email = %s AND status = 'approved'",
    $user_email
));

// Complaints
$open_complaints = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $complaint_table WHERE user_id = %d AND status = 'open'",
    $user_id
));

$resolved_complaints = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $complaint_table WHERE user_id = %d AND status = 'resolved'",
    $user_id
));

?>

<style>

/* PAGE */
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f5f7fa;
}

/* MAIN CONTAINER */
.container {
    max-width: 1100px;
    margin: 40px auto;
}

/* HEADER */
.user-meta {
    background: linear-gradient(135deg, #1e7f4f, #2ecc71);
    color: #fff;
    padding: 30px;
    border-radius: 18px;
}

.user-meta h1 {
    font-size: 40px;
    margin-bottom: 20px;
    color: #fff;
}

/* STATS */
.stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin: 25px 0;
}

.card {
    background: #29b466;
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
	height: 80px;
}
.section-box h2 {
    font-size: 25px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 20px;
}
.card h3 {
    font-size: 14px;
    color: #fff;
    margin: 0;
}

.card p {
    font-size: 20px;
    font-weight: 500;
    margin: 0px;
    background: #fff;
    width: 40px;
    height: 40px;
    border-radius: 50px;
    text-align: center;
    line-height: 40px;
    color: #26a961;
}

/* SECTION */
.section-box {
    background: #fff;
    padding: 25px;
    border-radius: 14px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.section-box ul {
    list-style: none;
    padding: 0;
}

.section-box li {
    display: flex;
    justify-content: space-between;
    background: #f9fbfc;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 10px;
}

/* STATUS */
.status-pill {
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: bold;
    display: flex;
    align-items: center;
    height: 35px;
}

.status-approved { background: #e6f7ee; color: #27ae60; }
.status-pending { background: #fff4e5; color: #f39c12; }
.status-declined { background: #fdecea; color: #e74c3c; }
.status-open { background: #fdecea; color: #e74c3c; }
.status-resolved { background: #e6f7ee; color: #27ae60; }

.loan-date {
    font-size: 12px;
    color: #888;
}

</style>

<div class="container">

    <!-- HEADER -->
    <div class="user-meta">
        <h1><?php echo esc_html($current_user->display_name); ?></h1>
        <p><?php echo esc_html($current_user->user_email); ?></p>
        <p>Member since <?php echo date("M Y", strtotime($current_user->user_registered)); ?></p>
    </div>

    <!-- DYNAMIC STATS -->
    <div class="stats">
        <div class="card">
            <h3>Active Loans</h3>
            <p><?php echo $active_loans ? $active_loans : 0; ?></p>
        </div>

        <div class="card">
            <h3>Total Applications</h3>
            <p><?php echo $total_loans ? $total_loans : 0; ?></p>
        </div>

        <div class="card">
            <h3>Open Complaints</h3>
            <p><?php echo $open_complaints ? $open_complaints : 0; ?></p>
        </div>

        <div class="card">
            <h3>Resolved Tickets</h3>
            <p><?php echo $resolved_complaints ? $resolved_complaints : 0; ?></p>
        </div>
    </div>

    <!-- LOANS -->
    <div class="section-box">
        <h2>Your Loan Applications</h2>

        <?php
        if (!empty($loans)) :
            echo '<ul>';
            foreach ($loans as $loan) : ?>
                <li>
                    <div>
                        <strong><?php echo esc_html($loan->loan_type); ?></strong><br>
                        <span class="loan-date">
                            ₹<?php echo number_format($loan->amount); ?> |
                            <?php echo date("d M Y", strtotime($loan->submitted_at)); ?>
                        </span>
                    </div>

                    <span class="status-pill status-<?php echo esc_attr($loan->status); ?>">
                        <?php echo ucfirst($loan->status); ?>
                    </span>
                </li>
            <?php endforeach;
            echo '</ul>';
        else :
            echo '<p>No loans found.</p>';
        endif;
        ?>
    </div>

    <!-- COMPLAINTS -->
    <div class="section-box">
        <h2>Your Complaints</h2>

        <?php
        $complaints = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $complaint_table WHERE user_id = %d ORDER BY created_at DESC",
            $user_id
        ));

        if (!empty($complaints)) :
            echo '<ul>';
            foreach ($complaints as $item) : ?>
                <li>
                    <div>
                        <strong>[<?php echo esc_html($item->ticket_id); ?>] <?php echo esc_html($item->subject); ?></strong><br>
                        <span class="loan-date">
                            <?php echo date("d M Y", strtotime($item->created_at)); ?>
                        </span>
                    </div>

                    <span class="status-pill status-<?php echo esc_attr($item->status); ?>">
                        <?php echo ($item->status == 'resolved') ? 'Closed' : 'Open'; ?>
                    </span>
                </li>
            <?php endforeach;
            echo '</ul>';
        else :
            echo '<p>No complaints found.</p>';
        endif;
        ?>
    </div>

</div>

<?php get_footer(); ?>