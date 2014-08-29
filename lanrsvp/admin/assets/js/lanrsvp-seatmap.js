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

        var seats = getStoredSeatmap(); // Array holding the status for all seats
        var mapSize = getGridSize(seats);
        var numberOfRows = mapSize[0]; // Initial number of rows for the seat map
        var numberOfColumns = mapSize[1]; // Initial columns of rows for the seat map

        var mouseIsDown = false; // Variable keeping track of when the mouse is down
        var currentRow = undefined; // Variable showing which row we are currently hovering
        var currentColumn = undefined; // Variable showing which column we are currently hovering
        var refreshingCells = false; // When refreshingCells === true, we cannot paint any new cells

        drawGrid(numberOfRows, numberOfColumns);
        if (seats.length > 0) {
            drawSeats();
        }

        function getStoredSeatmap() {
            var seats = [];
            var count = 0;
            if (LanRsvpAdmin !== null && LanRsvpAdmin.seatmap !== null) {
                for (var i = 0; i < LanRsvpAdmin.seatmap.length; i++) {
                    var seat = LanRsvpAdmin.seatmap[i];
                    if ('seat_column' in seat && 'seat_row' in seat && 'user_id' in seat) {
                        count++;
                        var row = seat['seat_row'];
                        var column = seat['seat_column'];
                        if (seats[row] === undefined) {
                            seats[row] = Array();
                        }
                        seats[row][column] = Object();
                        if (seat['user_id'] === null) {
                            seats[row][column]['status'] = 'free';
                        } else {
                            seats[row][column]['status'] = 'busy';
                            seats[row][column]['user_id'] = seat['user_id'];
                        }
                    }
                }
            }
            writeDebug(count + ' existing seats loaded');
            return seats;
        }

        function getGridSize(seats) {
            var maxRow = 9;
            var maxCol = 9;
            if (seats.length > 0) {
                maxRow = seats.length;
                maxCol = 0;
                for (var i = 0; i < seats.length; i++) {
                    if (seats[i] !== undefined && seats[i].length > maxCol) {
                        maxCol = seats[i].length;
                    }
                }
            }
            return [maxRow + 1, maxCol + 1];
        }

        function drawGrid (rows, columns) {
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
        }

        function drawSeats() {
            /*
            If we have resized the seat map with seats already assigned, paint the seats painted prior
            to the map being resized.
            */
            refreshingCells = true;
            for (var row = 0; row < seats.length; row++) {
                if (seats[row] !== undefined) {
                    for (var col = 0; col < seats[row].length; col++) {
                        if (seats[row][col] !== undefined) {
                            paintSeat(row,col);
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
                    setSeatStatus(currentRow,currentColumn);

                    if (mouseIsDown && !refreshingCells) {
                        if (toggleSeatStatus(currentRow, currentColumn)) {
                            paintSeat(currentRow, currentColumn);
                        }

                    }
                }
            }
        }

        function mouseDownListener() {
            mouseIsDown = true;
            if (!refreshingCells) {
                if (toggleSeatStatus(currentRow, currentColumn)) {
                    paintSeat(currentRow, currentColumn);
                }
            }
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
            for (var row = 0; row < seats.length; row++) {
                if (seats[row] !== undefined) {
                    for (var col = 0; col < seats[row].length; col++) {
                        if (seats[row][col] !== undefined) {
                            seats[row][col]['donottoggle'] = 0;
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

        function toggleSeatStatus (row, column) {

            // Initialize row if needed
            if (seats[row] === undefined) {
                seats[row] = Array();
            }

            // Initialize column if needed
            if (seats[row][column] === undefined) {
                seats[row][column] = Object();
            }

            if (seats[row][column]['donottoggle'] === 1) {
                // If we already have painted this cell during this
                // mousedown, we don't paint it again.
                return false;
            }

            var hasToggled = false;

            // Change status (undefined => 'free', 'free' => undefined)
            switch (seats[row][column]['status']) {
                case undefined:
                    hasToggled = true;
                    seats[row][column]['status'] = 'free';
                    break;
                case 'free':
                    hasToggled = true;
                    seats[row][column]['status'] = undefined;
                    break;
                default:
                    break;
            }

            // Variable to make sure we don't paint this cell again during this
            // "paint session". Reset every time mouseUpListener is called
            seats[row][column]['donottoggle'] = 1;

            return hasToggled;
        }

        var storeTimeout;
        function paintSeat (row, column) {
            clearTimeout(storeTimeout);

            switch (seats[row][column]['status']) {
                case undefined:
                    context.clearRect(
                        column * cellSize + 1,
                        row * cellSize + 1,
                        cellSize - 1,
                        cellSize - 1
                    );
                    break;
                case 'free':
                    context.fillStyle="#138e10";
                    context.fillRect(
                        column * cellSize + 1,
                        row * cellSize + 1,
                        cellSize - 1,
                        cellSize - 1
                    );
                    break;
                case 'busy':
                    context.fillStyle="#9C1616";
                    context.fillRect(
                        column * cellSize + 1,
                        row * cellSize + 1,
                        cellSize - 1,
                        cellSize - 1
                    );
                    break;
                default:
                    break;
            }

            storeTimeout = setTimeout(function(){
                // Store 'seats' to sessionStorage if the browser supports it.
                if (typeof(Storage) !== "undefined") {
                    writeDebug('Storing seats into sessionStorage ...');
                    sessionStorage.setItem('seats', JSON.stringify(seats));
                } else {
                    console.log('Error! browser does not support sessionStorage. Event creation cannot continue.');
                }
            }, 500);

        }

        function setSeatStatus(row, column) {
            $('#lanrsvp-seat-row').text(row);
            $('#lanrsvp-seat-column').text(column);

            var status = '';
            if (seats[row] === undefined || seats[row][column] === undefined ||
                seats[row][column]['status'] === undefined) {
                status = 'Not defined.';
            } else if (seats[row][column]['status'] == 'free') {
                status = 'Available.';
            } else {
                status = '<i class="fa fa-refresh fa-spin"></i>';

                var data = {
                    'action': 'get_attendee',
                    'event_id': LanRsvpAdmin.event_id,
                    'user_id': seats[row][column]['user_id']
                };

                $.post( ajaxurl, data, function(response) {
                    $('#lanrsvp-seat-status').text(response);
                });
            }
            $('#lanrsvp-seat-status').html(status);
        }

        $('input[name="lanrsvp-seatmap-cols"]').change(function() {
            handleSeatmapUpdate();
        });

        $('input[name="lanrsvp-seatmap-rows"]').change(function() {
            handleSeatmapUpdate();
        });

        var drawTimeout;
        function handleSeatmapUpdate() {
            numberOfRows = $('input[name="lanrsvp-seatmap-rows"]').val();
            numberOfColumns = $('input[name="lanrsvp-seatmap-cols"]').val();
            clearTimeout(drawTimeout);
            drawTimeout = setTimeout(function() {
                drawGrid(numberOfRows, numberOfColumns);
            }, 250);
        }

        $('.lanrsvp-save-grid').click(function(e) {
            e.preventDefault();
        });

	});
}(jQuery));