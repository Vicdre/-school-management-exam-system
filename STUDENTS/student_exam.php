<?php 
include('class/User.php');
$user = new User();
$user->loginStatus();
include('include/header.php');
include('ExamDatabase.php');

$db = new ExamDatabase();
$conn = $db->getConnection();

$examList = [];
$query = "SELECT * FROM exams ORDER BY id DESC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $examList[] = $row;
    }
}
?>
<title>Student Management System</title>
<link rel="stylesheet" href="css/style.css">
<?php include('include/container.php');?>
<div class="container contact">	
	<h2>Student Management System</h2>	
	<?php include 'menu.php'; ?>
<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">   
		<a href="#"><strong><span class="fa fa-dashboard"></span> My Exams</strong></a>
		<hr>		
	
		<div id="examListContainer" class="mt-3">
                <?php foreach ($examList as $exam): ?>
                    <div class="exam-box" data-id="<?php echo $exam['id']; ?>">
                        <span><strong><?php echo htmlspecialchars($exam['title']); ?></strong> 
                            <small class="text-muted">(<?php echo $exam['exam_date']; ?>)</small></span>
                            <div class="exam-actions">
                                <a href="student_examination.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-primary btn-sm">
                                    Start Examination
                                </a>
                            </div>

                    </div>
                <?php endforeach; ?>
      	</div>
	</div>
</div>	
<?php include('include/footer.php');?>
<style>
.exam-box {
    border: 1px solid #333;
    padding: 10px 15px;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
}

.exam-actions .btn {
    margin-left: 5px;
}
.form-check-input {
    margin-right: 10px;
  }
</style>