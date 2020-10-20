<!doctype html>
<html>
<head><title>Manajemen Meeting</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>
<body>
<?php
require_once('database.php');
$mysql = new Database();
$mysql->connectToDatabase();



?>
 <div class="container p-3">
<h3>Group List </h3> <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal">Add Member</button></br></br>
 <?php
foreach ($mysql->getAllGroup() as $c){
	echo "<h4>".$c['group_name']."</h4>";
?>
        <table id="example" class="table" style="width:100%">
            <thead class="thead-dark">
                <tr>
                <th scope="col">ID</th>
				<th scope="col">Members Name</th>
                <th scope="col">Members Email</th>
                <th scope="col">Action</th>
                </tr>
            </thead>
            
            <tbody>
              
        <?php 
        foreach ($mysql->getGroupMembers($c['group_id']) as $q){ ?>
					<tr>
                <td><?= $q['member_id'] ?></td>
                <td><?= $q['member_name'] ?></td>
                <td><?= $q['member_email'] ?></td>
                <td>
                    <div class="row">
                        
                        <div>
                            <button class="btn btn-warning float-right" data-toggle="modal" data-target="#myModal<?php echo $q['member_id']; ?>" >Edit</button>
                        </div>
                        <div class="ml-2">
                            <button class="btn btn-danger float-right" data-toggle="modal" data-target="#delete<?php echo $q['member_id']; ?>" >Delete</button>
                        </div>
                    </div>
                </td>
                </tr>

              
              <!-- Modal Edit -->
                <div class="modal fade" id="myModal<?php echo $q['member_id']; ?>" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Member</h5>
                      </div>
                      <div class="modal-body">
                            <form id="form1" action="member_edit.php" method="POST">
                            <input type="hidden" name="id" value="<?= $q['member_id']?>"/>
                            <div class="form-group" >
                            <div><label>Name</label></div>
                            <div><input type='text' class='form-control' value="<?= $q['member_name'] ?>" placeholder='Name' name='name' id='name'></div>
                            </div>
                            
                            <div class="form-group" >
                            <div><label>Email</label></div>
                            <div><input type='text' class='form-control' value="<?= $q['member_email'] ?>" placeholder='Email' name='email' id='email'></div>
                            </div>

                        
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancle</button>
                        <button type="Submit" class="btn btn-primary">Save</button>
                                
                      </div>
                      </form>
                    </div>
                  </div>
                </div>



                <!-- Modal Delete -->
                <div class="modal fade" id="delete<?php echo $q['member_id']; ?>" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Member</h5>
                      </div>
                      <div class="modal-body">
                            <form id="form1" action="member_delete.php" method="POST">
                            <input type="hidden" id="id" name="id" value="<?php echo $q['member_id']; ?>"/>77777777777777777777777777777777777777777777777777777777777777
                            <div class="form-group" >
                              Are you sure you want to <strong>DELETE <?= $q['member_name'] ?> </strong>?
                            </div>

                        
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancle</button>
                        <button type="Submit" class="btn btn-danger">Delete</button>
                                
                      </div>
                      </form>
                    </div>
                  </div>
                </div>
            
                <?php } ?>
                
            </tbody>
        </table>
        <br>
        <br>
				<?php } ?>
                   
				   </div>

<!-- Modal Add -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Member</h5>
      </div>
      <div class="modal-body">
            <form id="form1" action="Member_add.php" method="POST">
            <div class="form-group">
            <div>
            <label> Program Studi Name</label>
            </div>
	            <select class="form-control" id="gid" name="gid" >
		            <option value="" selected>--Pilih Prodi--</option>
                    <option value="1">Teknik Informatika</option>
                    <option value="2">Teknik Kimia</option>
                    <option value="3">Teknik Elektro</option>
                    <option value="11">Teknologi Pangan</option>
                    <option value="12">Teknik Industri</option>
                </select>
            </div>
            <div class="form-group" >
            <div><label>Name</label></div>
            <div><input type='text' class='form-control' placeholder='Name' name='name' id='name'></div>
            </div>
            
            <div class="form-group" >
            <div><label>Email</label></div>
            <div><input type='text' class='form-control' placeholder='Email' name='email' id='email'></div>
            </div>

        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancle</button>
        <button type="submit" class="btn btn-primary">Save</button>
                
      </div>
      </form>
    </div>
  </div>
</div>







<!--<script>
function cek(str) {
  var xhttp;
  if (str == "") {
    document.getElementById("txtHint").innerHTML = "";
    return;
  }
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
    document.getElementById("txtHint").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "adduser.php?val="+str, true);
  xhttp.send();
}
</script>--->


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>


</html>