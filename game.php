<?php
session_start();

// Task 6: Check all 8 winning lines
function checkWinner($board)
{
    $lines = array(
        array(0, 1, 2), array(3, 4, 5), array(6, 7, 8), // rows
        array(0, 3, 6), array(1, 4, 7), array(2, 5, 8), // columns
        array(0, 4, 8), array(2, 4, 6)                  // diagonals
    );
    foreach ($lines as $line)
    {
        $a = $line[0];
        $b = $line[1];
        $c = $line[2];
        if ($board[$a] != "" && $board[$a] == $board[$b] && $board[$b] == $board[$c])
        {
            return $board[$a]; // returns "X" or "O"
        }
    }
    return ""; // nobody has won yet
}

// Task 7: Check if all 9 squares are filled
function isFull($board)
{
    foreach ($board as $cell)
    {
        if ($cell == "") { return false; }
    }
    return true;
}

if (isset($_POST["new_game"]))
{
    $_SESSION["board"] = array_fill(0, 9, "");
    $_SESSION["turn"] = "X";
    $_SESSION["name_x"] = trim($_POST["player_x"]);
    $_SESSION["name_o"] = trim($_POST["player_o"]);
    if ($_SESSION["name_x"] == "") { $_SESSION["name_x"] = "Player X"; }
    if ($_SESSION["name_o"] == "") { $_SESSION["name_o"] = "Player O"; }
    if (!isset($_SESSION["score_x"])) { $_SESSION["score_x"] = 0; }
    if (!isset($_SESSION["score_o"])) { $_SESSION["score_o"] = 0; }
}

if (!isset($_SESSION["board"]))
{
    header("Location: start.html");
    exit;
}

$board = $_SESSION["board"];
$turn = $_SESSION["turn"];
$winner = checkWinner($board);
$draw = false;

// Tasks 5 & 6C: Handle square clicks, place marks, update score, and switch turns
if (isset($_POST["cell"]) && $winner == "")
{
    $i = (int) $_POST["cell"];
    if ($board[$i] == "")
    {
        $board[$i] = $turn;
        $winner = checkWinner($board);
        if ($winner != "")
        {
            $_SESSION["score_" . strtolower($winner)]++;
        }
        else if (isFull($board))
        {
            $draw = true;
        }
        else
        {
            $turn = ($turn == "X") ? "O" : "X";
        }
    }
    $_SESSION["board"] = $board;
    $_SESSION["turn"] = $turn;
}

// Task 7C: Ensure draw state persists on page refresh
if ($winner == "" && isFull($board)) { $draw = true; }

$nameOf = array("X" => $_SESSION["name_x"], "O" => $_SESSION["name_o"]);
?>
<html>
<head>
    <title>Tic Tac Toe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Tic Tac Toe</h1>
    <p class="score">
        <?php echo htmlspecialchars($_SESSION["name_x"]); ?> (X): <b><?php echo $_SESSION["score_x"]; ?></b>
        &nbsp; | &nbsp;
        <?php echo htmlspecialchars($_SESSION["name_o"]); ?> (O): <b><?php echo $_SESSION["score_o"]; ?></b>
    </p>
    <p class="status">
        <?php
        // Task 6D: Show winner, draw, or current turn
        if ($winner != "")
        {
            echo "Winner: " . htmlspecialchars($nameOf[$winner]) . " (" . $winner . ")";
        }
        else if ($draw)
        {
            echo "It is a draw!";
        }
        else
        {
            echo "Turn: " . htmlspecialchars($nameOf[$turn]) . " (" . $turn . ")";
        }
        ?>
    </p>
    <form method="post" action="game.php">
        <table class="board">
            <?php
            for ($row = 0; $row < 3; $row++)
            {
                echo "<tr>";
                for ($col = 0; $col < 3; $col++)
                {
                    $i = $row * 3 + $col;
                    echo "<td>";
                    if ($board[$i] != "")
                    {
                        echo "<span class='mark " . $board[$i] . "'>" . $board[$i] . "</span>";
                    }
                    // Task 7B: Hide buttons once someone wins or draws
                    else if ($winner == "" && !$draw)
                    {
                        echo "<button type='submit' name='cell' value='$i'>&nbsp;</button>";
                    }
                    echo "</td>";
                }
                echo "</tr>";
            }
            ?>
        </table>
    </form>
    <p>
        <a href="reset.php">Play again</a> |
        <a href="start.html">New players</a>
    </p>
</body>
</html>