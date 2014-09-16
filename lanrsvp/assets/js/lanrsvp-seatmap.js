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
        var cellSize = 20; // How many pixels * pixels each seat cell should be

        var seats = getStoredSeatmap(); // Array holding the status for all seats
        window.seats = seats;
        var mapSize = getGridSize(seats);
        window.seatmapRowSize = mapSize[0]; // Initial number of rows for the seat map
        $('input[name="lanrsvp-seatmap-rows"]').val(mapSize[0]);
        window.seatmapColSize = mapSize[1]; // Initial columns of rows for the seat map
        $('input[name="lanrsvp-seatmap-cols"]').val(mapSize[1]);

        var canSignUp = seatmap_data.canSignUp;
        var isAdmin = seatmap_data.isAdmin;

        var mouseIsDown = false; // Variable keeping track of when the mouse is down
        var paintedOnMouseDown = Array(); // Array to keep track of which seats are painted on each mousedown

        var currentCell = [undefined, undefined];
        window.chosenSeat = [undefined, undefined]; // For users signing up, one seat can be chosen at a time

        var refreshingCells = false; // When refreshingCells === true, we cannot paint any new cells

        drawSeatmap();

        setInterval(function(){
            var data = {
                action: 'get_seatmap',
                event_id: seatmap_data['event_id']
            };

            $.post(seatmap_data.ajaxurl, data, function(response) {
                if (response !== undefined && response.length > 0) {
                    refreshingCells = true;
                    var refreshed_seats = response;
                    for (var i = 0; i < refreshed_seats.length; i++) {
                        var seat = refreshed_seats[i];
                        if ('seat_column' in seat && 'seat_row' in seat && 'user_id' in seat) {
                            var row = seat['seat_row'];
                            var column = seat['seat_column'];
                            var repaint_seat = false;

                            /*
                            Scenario 1:

                            In case a seat went from undefined to 'busy' since the page was loaded. This can happen if:
                            1) an admin opens an event for editing in the admin panel
                            2) then, a user reserves a seat
                            3) the admin removes that seat, not knowing it was taken after he loaded the page
                            4) this code gets run

                            Or in case a seat went from 'free' to 'busy' since the page was loaded.
                            */
                            if (seat['user_id'] !== null &&
                                (
                                 seats[row] !== undefined ||
                                 seats[row][column] === undefined ||
                                 seats[row][column]['status'] === 'free'
                                )
                            ){
                                repaint_seat = true;
                                seats[row][column]['status'] = 'busy';
                                seats[row][column]['user_id'] = seat['user_id'];
                                seats[row][column]['first_name'] = seat['first_name'];
                                seats[row][column]['last_name'] = seat['last_name'];
                            }

                            /*
                            Scenario 2:
                            In case a seat went from 'busy' to 'free' since the page was loaded.
                            */
                            if (seat['user_id'] === null &&
                                seats[row] !== undefined &&
                                seats[row][column] !== undefined &&
                                seats[row][column]['status'] === 'busy'
                            ){
                                repaint_seat = true;
                                seats[row][column] = {};
                                seats[row][column]['status'] = 'free';
                            }

                            if (repaint_seat) {
                                paintSeat([row,column], seats[row][column]['status']);
                            }
                        }
                    }
                    refreshingCells = false;
                }
            }, 'json');
        }, 5000);

        function getStoredSeatmap() {
            var seats = [];
            var count = 0;
            if (seatmap_data !== undefined && seatmap_data['seats'] !== undefined) {
                for (var i = 0; i < seatmap_data['seats'].length; i++) {
                    var seat = seatmap_data['seats'][i];
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
                            seats[row][column]['first_name'] = seat['first_name'];
                            seats[row][column]['last_name'] = seat['last_name'];
                        }
                    }
                }
            }
            writeDebug(count + ' existing seats loaded');
            return seats;
        }

        function getGridSize(seats) {
            var rows = 0;
            var cols = 0;
            if (seats.length > 0) {
                rows = seats.length;
                for (var i = 0; i < seats.length; i++) {
                    if (seats[i] !== undefined && seats[i].length > cols) {
                        cols = seats[i].length;
                    }
                }
            }

            if (rows === 0) {
                rows = 9;
            } else if (rows < 4) {
                rows = 4;
            }

            if (cols === 0) {
                cols = 9;
            } else if (cols < 4) {
                cols = 4;
            }

            return [rows + 1, cols + 1];
        }

        function drawGrid (rows, columns) {
            var gridWidth = columns * cellSize;
            var gridHeight = rows * cellSize;

            writeDebug('drawGrid: gridWidth ' + gridWidth + ', gridHeight ' + gridHeight);

            canvas = $('#lanrsvp-seatmap-map > canvas');
            canvas.attr({
                width: gridWidth + 1, // + 1 for the border
                height: gridHeight + 1
            });
            canvas = canvas.get(0);

            canvas.addEventListener('mousemove', mouseMoveListener, false);
            if (canSignUp || isAdmin) {
                canvas.addEventListener('mousedown', mouseDownListener, false);
                canvas.addEventListener('mouseup', mouseUpListener, false);
            }

            context = canvas.getContext("2d");

            context.fillStyle="#efefef";
            context.fillRect(
                0,
                0,
                gridWidth + 1,
                gridHeight + 1
            );
            context.clearRect(
                cellSize,
                cellSize,
                gridWidth + 1 - 2 * cellSize,
                gridHeight + 1 - 2 * cellSize
            );

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

            context.strokeStyle = "#666666";
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
                            paintSeat([row,col],seats[row][col]['status']);
                        }
                    }
                }
            }
            refreshingCells = false;
        }

        function mouseMoveListener(evt) {
            var mousePos = getMousePos(canvas, evt);
            var row = Math.floor( (mousePos.y - 1) / cellSize);
            var col = Math.floor( (mousePos.x - 1) / cellSize);

            if (col !== -1 && row !== -1) {
                if (col !== currentCell[1] || row !== currentCell[0]) {
                    currentCell = [row, col];

                    if (withinBounds()) {
                        setSeatStatus(currentCell);

                        if (isAdmin && mouseIsDown && !refreshingCells) {
                            if (toggleSeatStatus(currentCell)) {
                                paintSeat(currentCell, getSeatStatus(currentCell));
                            }

                        }
                    }
                }
            }
        }

        function getSeatStatus(seat) {
            var row = seat[0];
            var col = seat[1];
            if (seats[row] !== undefined && seats[row][col] !== undefined) {
                return seats[row][col]['status'];
            } else {
                return undefined;
            }
        }

        function mouseDownListener() {
            mouseIsDown = true;
            if (!refreshingCells && (canSignUp || isAdmin)) {
                if (withinBounds()) {
                    var row = currentCell[0];
                    var col = currentCell[1];
                    if (toggleSeatStatus(currentCell)) {
                        paintSeat(currentCell, getSeatStatus(currentCell));
                    }
                }
            }
        }

        function withinBounds() {
            return (
                currentCell[0] !== 0 &&
                currentCell[1] !== 0 &&
                currentCell[0] !== (window.seatmapRowSize - 1) &&
                currentCell[1] !== (window.seatmapColSize - 1));
        }

        var storeSeatsTimeout;
        function mouseUpListener() {
            mouseIsDown = false;
            paintedOnMouseDown = Array();
            storeSeatsTimeout = setTimeout(function(){
                window.seats = seats;
            }, 500);
        }

        function getMousePos(canvas, evt) {
            var rect = canvas.getBoundingClientRect();
            return {
                x: evt.clientX - rect.left,
                y: evt.clientY - rect.top
            };
        }

        function toggleSeatStatus (cell) {

            var row = cell[0];
            var col = cell[1];

            // Initialize row if needed
            if (seats[row] === undefined) {
                seats[row] = Array();
            }

            // Initialize column if needed
            if (seats[row][col] === undefined) {
                seats[row][col] = Object();
            }

            if (isAdmin) {
                // If we already have painted this cell during this
                // mousedown, we don't paint it again.
                if (paintedOnMouseDown[row] !== undefined && paintedOnMouseDown[row][col] !== undefined) {
                    return;
                }
            }

            var hasToggled = false;

            if (isAdmin) {
                switch (seats[row][col]['status']) {
                    case undefined:
                        hasToggled = true;
                        seats[row][col]['status'] = 'free';
                        break;
                    case 'free':
                        hasToggled = true;
                        delete seats[row][col];
                        break;
                    default:
                        break;
                }
            } else if (canSignUp && seats[row][col]['status'] == 'free') {

                // Reset any previous chosen seat to 'free'
                if (window.chosenSeat[0] !== undefined && window.chosenSeat[1] !== undefined) {
                    var chosenRow = window.chosenSeat[0];
                    var chosenCol = window.chosenSeat[1];
                    seats[chosenRow][chosenCol]['status'] = 'free';
                    paintSeat(chosenSeat,'free');
                }

                // Set the new chosen seat
                window.chosenSeat[0] = row;
                window.chosenSeat[1] = col;

                hasToggled = true;
                seats[row][col]['status'] = 'busy';
            }

            if (isAdmin) {
                // Variable to make sure we don't paint this cell again during this
                // "paint session". Reset every time mouseUpListener is called
                if (paintedOnMouseDown[row] === undefined) {
                    paintedOnMouseDown[row] = Array();
                }
                paintedOnMouseDown[row][col] = true;
            }

            return hasToggled;
        }

        function paintSeat (cell, status) {
            clearTimeout(storeSeatsTimeout);

            var row = cell[0];
            var col = cell[1];

            switch (status) {
                case undefined:
                    context.clearRect(
                        col * cellSize + 1,
                        row * cellSize + 1,
                        cellSize - 1,
                        cellSize - 1
                    );
                    break;
                case 'free':
                    context.fillStyle = "#138e10";
                    context.fillRect(
                        col * cellSize + 1,
                        row * cellSize + 1,
                        cellSize - 1,
                        cellSize - 1
                    );
                    break;
                case 'busy':

                    if (row === chosenSeat[0] && col === chosenSeat[1]) {
                        context.fillStyle = "#000000";
                    } else {
                        context.fillStyle = "#9C1616";
                    }

                    context.fillRect(
                        col * cellSize + 1,
                        row * cellSize + 1,
                        cellSize - 1,
                        cellSize - 1
                    );
                    break;
                default:
                    break;
            }

        }

        function setSeatStatus(cell) {
            var row = cell[0];
            var col = cell[1];

            $('#lanrsvp-seat-row').text(row);
            $('#lanrsvp-seat-column').text(col);

            var status = '';
            if (seats[row] === undefined || seats[row][col] === undefined ||
                seats[row][col]['status'] === undefined) {
                status = 'Not defined.';
            } else if (seats[row][col]['status'] == 'free') {
                status = 'Available.';
            } else if (seats[row][col]['status'] == 'busy') {
                status = 'Taken by ' + seats[row][col]['first_name'] + ' ' + seats[row][col]['last_name'] + '.';
            }
            $('#lanrsvp-seat-status').text(status);
        }

        $('input[name="lanrsvp-seatmap-cols"]').change(function() {
            window.seatmapRowSize = $('input[name="lanrsvp-seatmap-rows"]').val();
            window.seatmapColSize = $('input[name="lanrsvp-seatmap-cols"]').val();
            drawSeatmap();
        });

        $('input[name="lanrsvp-seatmap-rows"]').change(function() {
            window.seatmapRowSize = $('input[name="lanrsvp-seatmap-rows"]').val();
            window.seatmapColSize = $('input[name="lanrsvp-seatmap-cols"]').val();
            drawSeatmap();
        });

        var drawTimeout;
        function drawSeatmap() {
            clearTimeout(drawTimeout);
            drawTimeout = setTimeout(function() {
                drawGrid(window.seatmapRowSize, window.seatmapColSize);
                if (seats.length > 0) {
                    drawSeats();
                }
            }, 250);
        }

        $('.lanrsvp-save-grid').click(function(e) {
            e.preventDefault();
        });

	});
}(jQuery));