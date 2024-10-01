@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between">
            <div>
                <button id="loadTable" type="button" class="btn btn-primary">LIST</button>
                <button id="emptyTable" type="button" class="btn btn-warning">EMPTY</button>
            </div>
            <div class="d-flex">
                <form method="POST" action="{{ route('fetch') }}" class="me-2">
                    @csrf
                    <button type="submit" class="btn btn-secondary me-2">Update prices</button>
                </form>
                <form method="POST" action="{{ route('delete') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary">Delete prices</button>
                </form>
            </div>
        </div>


        <h2>Alko prices</h2>
        <div id="dataTable">
            <table class="table table-bordered" id="userTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Bottle Size</th>
                    <th>Price</th>
                    <th>Price GBP</th>
                    <th>orderamount</th>
                    <th>actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div id="paginationLinks" class="d-flex justify-content-center"></div>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#userTable').hide();
            $('#paginationLinks').hide();

            $('#loadTable').on('click', function() {
                loadTableData(1);
            });

            function loadTableData(page = 1) {
                $.ajax({
                    url: "{{ route('loadTableData') }}?page=" + page,
                    method: 'GET',
                    success: function(response) {
                        $('#userTable').show();
                        $('#paginationLinks').show();

                        $('#userTable tbody').html(response.tableData);

                        $('#paginationLinks').html(response.pagination);

                        $('#paginationLinks a').on('click', function(e) {
                            e.preventDefault();
                            var page = $(this).attr('href').split('page=')[1];
                            loadTableData(page);
                        });

                        // _________________
                        $('.add-btn').on('click', function() {
                            let number = $(this).data('number');

                            $.ajax({
                                url: "{{ route('updateOrderAmount') }}",
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    number: number,
                                    action: 'add'
                                },
                                success: function(response) {
                                    $('#orderAmount_' + number).text(response.newAmount);
                                    $('#price_' + number).text(response.price + ' €');
                                    $('#priceGBP_' + number).text(response.priceGBP + ' £');
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error: ' + error);
                                }
                            });
                        });

                        $('.clear-btn').on('click', function() {
                            let number = $(this).data('number');

                            $.ajax({
                                url: "{{ route('updateOrderAmount') }}",
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    number: number,
                                    action: 'clear'
                                },
                                success: function (response) {
                                    $('#orderAmount_' + number).text(response.newAmount);
                                    $('#price_' + number).text(response.price + ' €');
                                    $('#priceGBP_' + number).text(response.priceGBP + ' £');
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error: ' + error);
                                }
                            });
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log("Error: " + error);
                    }
                });
            }

            $('#emptyTable').on('click', function (e) {
                e.preventDefault();
                $('#userTable').hide();
                $('#paginationLinks').hide().html('');
                $('#userTable tbody').html('');
            })
        });


    </script>
@endsection



