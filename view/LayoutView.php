<?php
/**
  * Solution for assignment 2
  * @author Daniel Toll
  */
namespace view;

class LayoutView {
  public function render($registerNewUser, $isLoggedIn, iLayoutView $v, DateTimeView $dtv, iLayoutView $content = null) {
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Todo-app Example</title>
    <link rel="stylesheet" href="/style/custom.css">
  </head>
  <body>
    <h1>Project in PHP</h1>
    <?php
      if($registerNewUser){
        echo '<a href="./">Back to login</a>';
      } else {
        echo '<a href="?register">Register a new user</a>';
      }
      if ($isLoggedIn) {
        echo "<h2>Logged in</h2>";
      } else {
        echo "<h2>Not logged in</h2>";
    }
  ?>
    <div class="container" >
      <?php 
        echo $v->response();

        if($content)
          echo "<br />" . $content->response();

        $dtv->show();
      ?>
    </div>

    <div>
      <em>This site uses cookies to improve user experience. By continuing to browse the site you are agreeing to our use of cookies.</em>
    </div>
   </body>
</html>
<?php
  }
}
