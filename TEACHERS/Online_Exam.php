<?php 
include('class/User.php');
$user = new User();
$user->loginStatus();
$userDetail = $user->getUserDetails();
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

<title>Teacher Management System</title>
<?php include('include/container.php');?>
<div class="container contact">	
	<h2>Teacher Management System</h2>	
	<?php include('menu.php');?>
	<div class="table-responsive">		
		<a href="#"><strong><span class="fa fa-dashboard"></span> Online Exam</strong></a>
		<hr>
        <div class="table-responsive">		
            <form class="border p-3 rounded shadow-sm bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Online Exam</h4>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNewModal">
                        Add New +
                    </button>
                </div>
                <hr>
                <div id="examListContainer" class="mt-3">
                <?php foreach ($examList as $exam): ?>
                    <div class="exam-box" data-id="<?php echo $exam['id']; ?>">
                        <span><strong><?php echo htmlspecialchars($exam['title']); ?></strong> 
                            <small class="text-muted">(<?php echo $exam['exam_date']; ?>)</small></span>
                        <div class="exam-actions">
                            <button type="button" class="btn btn-default btn-sm btn-edit">Add Question</button>
                            <button type="button" class="btn btn-default btn-sm btn-update">Update</button>
                            <button type="button" class="btn btn-default btn-sm btn-delete">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </form>
        </div>
	</div>	
</div>	

<!-- Add New Modal -->
<div class="modal fade" id="addNewModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Exam</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <form id="addExamForm">
          <div class="mb-3">
            <label for="examTitle">Exam Title</label>
            <input type="text" class="form-control" id="examTitle" name="examTitle" >
          </div>
          <div class="mb-3">
            <label for="examDate">Exam Date</label>
            <input type="date" class="form-control" id="examDate" name="examDate" >
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success" form="addExamForm">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal (Add Question) -->
<div class="modal fade" id="editExamModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Exam - Add Question</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <form id="editExamForm">
          <div class="form-group">
            <label for="examQuestion">Question</label>
            <textarea class="form-control" id="examQuestion" name="examQuestion" required></textarea>
          </div>
          <div class="form-group mt-3">
            <label>Answer Choices</label>
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="form-check mt-2">
              <input type="text" class="form-control d-inline-block w-75 ml-2" name="choice[]" placeholder="Choice <?php echo chr(64 + $i); ?>" required>
              <input class="form-check-input" type="radio" name="correctAnswer" value="<?php echo $i; ?>" required>
            </div>
            <?php endfor; ?>
            <label>Select a correct answer using the radio button</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success" form="editExamForm">Save Question</button>
      </div>
    </div>
  </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateQuestionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Existing Question</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <form id="updateQuestionForm">
          <div class="form-group">
            <label for="updateQuestionSelector">Select Question to Edit</label>
            <select class="form-control" id="updateQuestionSelector">
              <option value="">-- Select Question --</option>
            </select>
          </div>
          <div class="form-group mt-3">
            <label for="updateExamQuestion">Question</label>
            <textarea class="form-control" id="updateExamQuestion" required></textarea>
          </div>
          <div class="form-group mt-3">
            <label>Answer Choices</label>
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="form-check mt-2">
              <input type="text" class="form-control d-inline-block w-75 ml-2" name="updateChoice[]" placeholder="Choice <?php echo chr(64 + $i); ?>" required>
              <input class="form-check-input" type="radio" name="updateCorrectAnswer" value="<?php echo $i; ?>" required>
            </div>
            <?php endfor; ?>
            <label>Select the correct answer using the radio button</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" form="updateQuestionForm">Update Question</button>
      </div>
    </div>
  </div>
</div>

<script>
$('#addExamForm').on('submit', function(e) {
  e.preventDefault();

  const title = $('#examTitle').val().trim();
  const exam_date = $('#examDate').val().trim();

  // Validation
  if (!title && !exam_date) {
    alert("Please provide both the Exam Title and Exam Date.");
    return;
  }
  if (!title) {
    alert("Exam Title cannot be blank or just spaces.");
    return;
  }
  if (!exam_date) {
    alert("Please select a valid Exam Date.");
    return;
  }

  // AJAX POST request
  $.post('add_exam.php', { title, exam_date }, function(response) {
    const examId = parseInt(response);
    if (!isNaN(examId)) {
      $('#examListContainer').append(`
        <div class="exam-box" data-id="${examId}">
          <span><strong>${title}</strong> <small class="text-muted">(${exam_date})</small></span>
          <div class="exam-actions">
            <button type="button" class="btn btn-default btn-sm btn-edit">Add Question</button>
            <button type="button" class="btn btn-default btn-sm btn-update">Update</button>
            <button type="button" class="btn btn-default btn-sm btn-delete">Delete</button>
          </div>
        </div>
      `);
      $('#addNewModal').modal('hide');
      $('#addExamForm')[0].reset();
      alert("New Exam Course added successfully.");
    } else {
      alert("Failed to add exam: " + response);
    }
  });
});



$('#examListContainer').on('click', '.btn-delete', function() {
  const examBox = $(this).closest('.exam-box');
  const examId = examBox.data('id');
  if (confirm("Are you sure?")) {
    $.post('delete_exam.php', { id: examId }, function(response) {
      alert(response);
      examBox.remove();
    });
  }
});

let currentExamId = null;
$('#examListContainer').on('click', '.btn-edit', function() {
  currentExamId = $(this).closest('.exam-box').data('id');
  $('#editExamForm')[0].reset();
  $('#editExamModal').modal('show');
});

$('#editExamForm').on('submit', function(e) {
  e.preventDefault();
  const question = $('#examQuestion').val().trim();
  const choices = $("input[name='choice[]']").map((_, el) => el.value.trim()).get();
  const correct = $("input[name='correctAnswer']:checked").val();

  if (question && correct && choices.every(c => c !== "") && currentExamId) {
    $.post('add_question.php', {
      exam_id: currentExamId,
      question,
      choice_1: choices[0],
      choice_2: choices[1],
      choice_3: choices[2],
      choice_4: choices[3],
      correct_answer: correct
    }, function(response) {
      alert(response);
      $('#editExamModal').modal('hide');
    });
  } else {
    alert("All fields are required.");
  }
});

let currentUpdateExamId = null;
let currentQuestionId = null;
$('#examListContainer').on('click', '.btn-update', function() {
  currentUpdateExamId = $(this).closest('.exam-box').data('id');
  $('#updateQuestionModal').modal('show');
  $('#updateQuestionSelector').empty().append('<option value="">-- Select Question --</option>');
  $.get('get_question.php', { exam_id: currentUpdateExamId }, function(data) {
    data.trim().split('\n').forEach(line => {
      const [id, number] = line.split('|');
      $('#updateQuestionSelector').append(`<option value="${id}">${number}</option>`);
    });
  });
});

$('#updateQuestionSelector').on('change', function() {
  currentQuestionId = this.value;
  if (!currentQuestionId) return;
  $.get('get_questions_list.php', { id: currentQuestionId }, function(data) {
    const [question, a, b, c, d, correct] = data.split('|');
    $('#updateExamQuestion').val(question);
    $('input[name="updateChoice[]"]').each((i, el) => $(el).val([a, b, c, d][i]));
    $('input[name="updateCorrectAnswer"]').each((_, el) => $(el).prop('checked', el.value == correct));
  });
});

$('#updateQuestionForm').on('submit', function(e) {
  e.preventDefault();
  const question = $('#updateExamQuestion').val().trim();
  const choices = $('input[name="updateChoice[]"]').map((_, el) => el.value.trim()).get();
  const correct = $('input[name="updateCorrectAnswer"]:checked').val();

  if (question && correct && choices.every(c => c !== "") && currentQuestionId) {
    $.post('update_question.php', {
      id: currentQuestionId,
      exam_id: currentUpdateExamId,
      question,
      choice_1: choices[0],
      choice_2: choices[1],
      choice_3: choices[2],
      choice_4: choices[3],
      correct_answer: correct
    }, function(response) {
      alert(response);
      $('#updateQuestionModal').modal('hide');
    });
  } else {
    alert("Please fill all fields and choose the correct answer.");
  }
});
</script>

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
