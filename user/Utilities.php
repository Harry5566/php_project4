<?php

function alertAndBack($msg = "")
{
  echo "<script>
          alert('$msg');
          window.history.back();
        </script>";
}

function alertGoBack($msg = "")
{
  echo "<script>
          alert('$msg');
          window.location = './pageMsgsList.php';
        </script>";
}



function alertGoTo($msg = "", $url = "./pageMsgsList.php")
{
  echo "<script>
          alert('$msg');
          window.location = '$url';
        </script>";
}

// 有預設值的參數有往最後放
function timeoutGoBack($time = 1000)
{
  echo "<script>
          setTimeout(() => window.location = './pageMsgsList.php', $time);
        </script>";
}


function goBack()
{
  echo "<button onclick='goBack()'>回上一頁</button>";
  echo "<script>
          // const goBack () => {}
          function goBack() {
            window.history.back();
          }
        </script>";
}
?>