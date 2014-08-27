(function ( $ ) {
	"use strict";
	$(function () {

        var debug = true;
        function writeDebug (message) {
            if (debug) {
                console.log(message);
            }
        }

        var canvas; // To be set by drawGrid()
        var context; // To be set by drawGrid()
        var cellSize = 30; // How many pixels * pixels each seat cell should be

        var numberOfRows = 10; // Initial number of rows for the seat map
        var numberOfColumns = 10; // Initial columns of rows for the seat map

        var seats = getExistingSeatmap(); // Array holding the status for all seats
        var mouseIsDown = false; // Variable keeping track of when the mouse is down
        var currentRow = undefined; // Variable showing which row we are currently hovering
        var currentColumn = undefined; // Variable showing which column we are currently hovering
        var refreshingCells = false; // When refreshingCells === true, we cannot paint any new cells

        drawSeatmap(numberOfRows, numberOfColumns);

        function getExistingSeatmap() {
            var seats = [];
            if (LanRsvpAdmin.seatmap !== undefined) {
                for (var i = 0; i < LanRsvpAdmin.seatmap.length; i++) {
                    var seat = LanRsvpAdmin.seatmap[i];
                    if ('seat_column' in seat && 'seat_row' in seat && 'user_id' in seat) {
                        var row = seat['seat_row'];
                        var column = seat['seat_column'];
                        if (seats[row] === undefined) {
                            seats[row] = Array();
                        }
                        seats[row][column] = {
                            'status': (seat['user_id'] === null ? 'free' : 'busy')
                        };
                    }
                }
            }
            console.log(seats);
            return seats;
        }

        function drawSeatmap (rows, columns) {
            var gridWidth = columns * cellSize;
            var gridHeight = rows * cellSize;

            writeDebug('drawGrid: gridWidth ' + gridWidth + ', gridHeight ' + gridHeight);

            canvas = $('#lanrsvp-seatmap');
            canvas.attr({
                width: gridWidth + 1, // + 1 for the border
                height: gridHeight + 1
            });
            canvas = canvas.get(0);

            canvas.addEventListener('mousemove', mouseMoveListener, false);
            canvas.addEventListener('mousedown', mouseDownListener, false);
            canvas.addEventListener('mouseup', mouseUpListener, false);

            context = canvas.getContext("2d");

            for (var x = 0; x <= gridHeight; x += cellSize) {
                context.moveTo(0.5 + x, 0);
                context.lineTo(0.5 + x, gridHeight);
            }

            if (gridWidth > gridHeight) {
                for (var y = gridHeight; y <= gridWidth; y += cellSize) {
                    context.moveTo(0.5 + y, 0);
                    context.lineTo(0.5 + y, gridHeight);
                }
            }

            for (var z = 0; z <= gridHeight; z += cellSize) {
                context.moveTo(0, 0.5 + z);
                context.lineTo(gridWidth, 0.5 + z);
            }

            context.strokeStyle = "black";
            context.stroke();

            /*
            If we have resized the seat map with seats already assigned, paint the seats painted prior
            to the map being resized.
            */
            refreshingCells = true;
            for (var i = 0; i < seats.length; i++) {
                if (seats[i] !== undefined) {
                    for (var j = 0; j < seats[i].length; j++) {
                        if (seats[i][j] !== undefined) {
                            if (seats[i][j]['status'] !== 'noset') {
                                switch (seats[i][j]['status']) {
                                    case 'free':
                                        context.fillStyle="#9c1616";
                                        break;
                                    case 'busy':
                                        context.fillStyle="#138e10";
                                        break;
                                    default:
                                        break;
                                }
                                context.fillRect(
                                    j * cellSize + 1,
                                    i * cellSize + 1,
                                    cellSize - 1,
                                    cellSize - 1
                                );
                            }
                        }
                    }
                }
            }
            refreshingCells = false;
        }

        function mouseMoveListener(evt) {
            var mousePos = getMousePos(canvas, evt);

            var column = Math.floor( (mousePos.x - 1) / cellSize);
            var row = Math.floor( (mousePos.y - 1) / cellSize);

            if (column !== -1 && row !== -1) {
                if (column !== currentColumn || row !== currentRow) {
                    currentRow = row;
                    currentColumn = column;
                    //writeDebug('hover: row ' + row + ', column ' + column);

                    paintCell(currentRow, currentColumn);
                }
            }
        }

        function mouseDownListener() {
            mouseIsDown = true;
            paintCell(currentRow, currentColumn);
        }

        function mouseUpListener() {

            /*
            Iterate over the seats array and reset the 'set' variable,
            preventing seats from being painted twice during the same
            mousedown.
             */

            mouseIsDown = false;

            var start = new Date().getMilliseconds();
            refreshingCells = true;
            for (var i = 0; i < seats.length; i++) {
                if (seats[i] !== undefined) {
                    for (var j = 0; j < seats[i].length; j++) {
                        if (seats[i][j] !== undefined) {
                            seats[i][j]['set'] = 0;
                        }
                    }
                }
            }
            refreshingCells = false;
            var end = new Date().getMilliseconds();
            var time = end - start;

            writeDebug("mouseUpListener: The 'set' variable of all seats was reset in: " + time);
        }

        function getMousePos(canvas, evt) {
            var rect = canvas.getBoundingClientRect();
            return {
                x: evt.clientX - rect.left,
                y: evt.clientY - rect.top
            };
        }

        function paintCell (row, column) {
            if (mouseIsDown && !refreshingCells) {
                if (seats[row] === undefined) {
                    seats[row] = Array();
                }

                if (seats[row][column] === undefined) {
                    seats[row][column] = new Object({
                        status : 'notset'
                    });
                } else {
                    if (seats[row][column]['set'] === 1) {
                        // If we already have painted this cell during this
                        // mousedown, we don't paint it again.
                        return;
                    }
                }

                if (seats[row][column]['status'] === 'notset') {
                    // Not initialized, set to 'free' and paint cell green
                    seats[row][column]['status'] = 'free';
                    context.fillStyle="#138e10";
                } else if (seats[row][column]['status'] === 'free') {
                    // free, now set to 'busy' and paint cell red
                    seats[row][column]['status'] = 'busy';
                    context.fillStyle="#9c1616";
                } else if (seats[row][column]['status'] === 'busy') {
                    // busy, now set to undefined and paint cell white (remove color)
                    seats[row][column]['status'] = 'notset';
                    context.clearRect(
                        column * cellSize + 1,
                        row * cellSize + 1,
                        cellSize - 1,
                        cellSize - 1
                    );
                }

                // Store 'seats' to sessionStorage if the browser supports it.
                if (typeof(Storage) !== "undefined") {
                    sessionStorage.setItem('seats', JSON.stringify(seats));
                } else {
                    console.log('Error! browser does not support sessionStorage. Event creation cannot continue.');
                }

                // Variable to make sure we don't paint this cell again during this
                // "paint session". Reset every time mouseUpListener is called
                seats[row][column]['set'] = 1;

                if (seats[row][column]['status'] !== 'notset') {
                    context.fillRect(
                        column * cellSize + 1,
                        row * cellSize + 1,
                        cellSize - 1,
                        cellSize - 1
                    );
                }
            }
        }

        var drawTimeout;

        $('input[name="lanrsvp-seatmap-cols"]').change(function() {
            handleSeatmapUpdate();
        });

        $('input[name="lanrsvp-seatmap-rows"]').change(function() {
            handleSeatmapUpdate();
        });

        function handleSeatmapUpdate() {
            numberOfRows = $('input[name="lanrsvp-seatmap-rows"]').val();
            numberOfColumns = $('input[name="lanrsvp-seatmap-cols"]').val();
            clearTimeout(drawTimeout);
            drawTimeout = setTimeout(function() {
                drawSeatmap(numberOfRows, numberOfColumns);
            }, 250);
        }

        $('.lanrsvp-save-grid').click(function(e) {
            e.preventDefault();
        });

	});
}(jQuery));