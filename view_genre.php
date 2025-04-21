<?php
include 'db_connect.php';

// Ensure `id` is set and valid
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Initialize variables to prevent undefined errors
$ptitle = "Unknown Genre";
$description = "No description available.";
$cover_photo = "default.jpg"; // Use a default cover photo if none exists

$qry = $conn->query("SELECT * FROM genres WHERE id = $id");

if ($qry && $qry->num_rows > 0) {
    $row = $qry->fetch_assoc(); // Fetch the row as an associative array
    foreach ($row as $k => $v) {
        if ($k == 'title') {
            $k = 'ptitle'; // Rename 'title' to 'ptitle' to avoid conflicts
        }
        $$k = $v;
    }
}
?>

<div class="col-lg-12">
    <div class="row">
        <div class="col-md-4">
            <center>
                <div class="d-flex img-thumbnail bg-gradient-1 position-relative" style="width: 12rem">
                    <img src="assets/uploads/<?php echo htmlspecialchars($cover_photo); ?>" 
                         alt="Cover Photo" 
                         style="object-fit: cover; max-width: 100%; height:14rem">
                    <span class="position-absolute" style="bottom:.5em; left:.5em">
                        <div class="bg-green rounded-circle d-flex justify-content-center align-items-center" 
                             style="width: 2rem; height: 2rem; cursor: pointer;" 
                             onclick="play_playlist()">
                            <i class="fa fa-play"></i>
                        </div>
                    </span>
                </div>
            </center>
        </div>
        <div class="col-md-8">
            <h5 class="text-white">Title: <?php echo ucwords(htmlspecialchars($ptitle)); ?></h5>
            <h6 class="text-white border-bottom border-primary"><b class="text-white">Description:</b></h6>
            <div class="text-white">
                <?php echo html_entity_decode($description); ?>
            </div>
            <table class="table bg-black">
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Artist</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 0;
                    $items = $conn->query("SELECT * FROM uploads WHERE genre_id = $id");

                    if ($items && $items->num_rows > 0):
                        while ($row = $items->fetch_assoc()):
                    ?>
                        <tr class="pitem" data-queue="<?php echo $i; ?>" 
                            data-id="<?php echo $row['id']; ?>" 
                            data-upath="<?php echo htmlspecialchars($row['upath']); ?>">
                            <td>
                                <span class="btn bg-gradient-success rounded-circle d-flex justify-content-center align-items-center" 
                                      style="width:30px;height:30px;z-index:2" 
                                      onclick="play_playlist(<?php echo $i; ?>)">
                                    <div class="fa fa-play text-white"></div>
                                </span>
                            </td>
                            <td>
                                <i class='fa fa-music text-gradient-primary mr-2'></i>
                                <?php echo ucwords(htmlspecialchars($row['title'])); ?>
                            </td>
                            <td>
                                <?php echo ucwords(htmlspecialchars($row['artist'])); ?>
                            </td>
                        </tr>
                    <?php 
                        $i++;
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="3" class="text-center text-white">No songs found in this genre.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function play_playlist(index = 0) {
    var src = {};
    $('.pitem').each(function(){
        var id = $(this).attr('data-id');
        var upath = $(this).attr('data-upath');
        var q = $(this).attr('data-queue');
        src[q] = {id: id, upath: 'assets/uploads/' + upath};
    });

    console.log(src);
    play_music(src, index);
}

$('#manage_plist').click(function(e) {
    e.preventDefault();
    uni_modal("Manage Playlist Music", 'manage_playlist_items.php?pid=<?php echo $id; ?>');
});
</script>
