@extends('layout.layout')




@section('content')
    <!-- Content Row -->
    <div class="row" id="bucket-list">
    </div>

    <!-- Button to Open Modal -->
    <a href="#" class="btn btn-primary btn-icon-split" data-toggle="modal" data-target="#addBucketModal">
        <span class="icon text-white-50">
            <i class="fas fa-flag"></i>
        </span>
        <span class="text">Add Bucket</span>
    </a>

    <!-- Modal for Adding a Bucket -->
    <div class="modal fade" id="addBucketModal" tabindex="-1" role="dialog" aria-labelledby="addBucketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBucketModalLabel">Add Bucket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addBucketForm">
                        <div class="form-group">
                            <label for="bucketName">Bucket Name</label>
                            <input type="text" class="form-control" id="bucketName" placeholder="Enter bucket name"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="isVersioning">Enable Versioning</label>
                            <select class="form-control" id="isVersioning" required>
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </div>
                        {{-- metadata --}}
                        <label for="">Metadata Section</label>
                        <div class=" " id="metadataContainer">
                            <div class="form-group row metadata-item" id="">
                                <div class="col-sm-9 px-2">
                                    <input type="text" class="form-control metadata-key" id=""
                                        placeholder="Enter metadata name" required>
                                </div>
                                <div class="col-sm-3 px-1">
                                    <select class="form-control p-0 metadata-type" id="MetadataType" required>
                                        <option value="integer">integer</option>
                                        <option value="string">string</option>
                                        <option value="datetime">datetime</option>
                                        <option value="decimal">decimal</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="addMetadata()">More Metadata</button>
                        <h6>System Metadata</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="string" id="ObjectNameCheckbox"
                                        name="ObjectName">
                                    <label class="form-check-label" for="ObjectNameCheckbox">
                                        System Metadata: Object Name
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="string" id="OwnerCheckbox"
                                        name="Owner">
                                    <label class="form-check-label" for="OwnerCheckbox">
                                        System Metadata: Owner
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="integer" id="SizeCheckbox"
                                        name="Size">
                                    <label class="form-check-label" for="SizeCheckbox">
                                        System Metadata: Size
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="datetime" id="CreateTimeCheckbox"
                                        name="CreateTime">
                                    <label class="form-check-label" for="CreateTimeCheckbox">
                                        System Metadata: CreateTime
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="datetime"
                                        id="LastModifiedCheckbox" name="LastModified">
                                    <label class="form-check-label" for="LastModifiedCheckbox">
                                        System Metadata: LastModified
                                    </label>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <h6>Optional System Metadata</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="string"
                                        id="ContentTypeCheckbox" name="ContentType">
                                    <label class="form-check-label" for="ContentTypeCheckbox">
                                        System Metadata: ContentType
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="datetime"
                                        id="ExpirationCheckbox" name="Expiration">
                                    <label class="form-check-label" for="ExpirationCheckbox">
                                        System Metadata: Expiration
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="string"
                                        id="ContentEncodingCheckbox" name="ContentEncoding">
                                    <label class="form-check-label" for="ContentEncodingCheckbox">
                                        System Metadata: ContentEncoding
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="datetime"
                                        id="ExpiresCheckbox" name="Expires">
                                    <label class="form-check-label" for="ExpiresCheckbox">
                                        System Metadata: Expires
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="integer"
                                        id="RetentionCheckbox" name="Retention">
                                    <label class="form-check-label" for="RetentionCheckbox">
                                        System Metadata: Retention
                                    </label>
                                </div>
                            </div> --}}
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addBucket()">Add Bucket</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const apiUrl = 'http://localhost:8011/bucket';
            // Fetch the bucket list from the API using Axios
            axios.get(apiUrl)
                .then(function(response) {
                    const buckets = response.data.Buckets;
                    const bucketList = document.getElementById('bucket-list');
                    console.log(response);

                    if (buckets.length > 0) {
                        buckets.forEach(function(bucket) {
                            let creationDate = new Date(bucket.CreationDate).toLocaleDateString();
                            // Create a new card for each bucket
                            const bucketCard = `
                            <div class="col-xl-3 col-md-6 mb-4">
                                <a href="/bucket/${bucket.BucketName}">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Bucket Name</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${bucket.BucketName}</div>
                                                </div>
                                                <div class="col-auto d-flex flex-column">
                                                    <i class="fas fa-server fa-2x text-gray-300"></i>
                                                    ${creationDate}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        `;
                            // Append the card to the bucket list
                            bucketList.innerHTML += bucketCard;
                        });
                    } else {
                        bucketList.innerHTML = '<p>No buckets found.</p>';
                    }
                })
                .catch(function(error) {
                    console.error("Error fetching bucket list:", error);
                    document.getElementById('bucket-list').innerHTML = '<p>Error loading buckets.</p>';
                });



        });

        // function addBucket() {
        //     const bucketName = document.getElementById('bucketName').value;
        //     const isVersioning = document.getElementById('isVersioning').value;

        //     if (!bucketName) {
        //         alert("Bucket name is required.");
        //         return;
        //     }

        //     const apiUrl = 'http://localhost:8011/bucketmeta';

        //     axios.post(apiUrl, {
        //             bucketName: bucketName,
        //             isVersioning: JSON.parse(isVersioning) // Convert to boolean
        //         })
        //         .then(response => {
        //             console.log('Bucket added:', response.data);
        //             $('#addBucketModal').modal('hide');
        //             location.reload();
        //         })
        //         .catch(error => {
        //             console.error('Error adding bucket:', error);
        //             // Show error message to user if needed
        //         });
        // }
        function addBucket() {
            // Get bucket name and versioning status
            const bucketName = document.getElementById("bucketName").value;
            const isVersioning = document.getElementById("isVersioning").value;

            // Collect metadata fields
            const metadataItems = document.querySelectorAll("#metadataContainer .metadata-item");
            const metadata = {};

            metadataItems.forEach(item => {
                const key = item.querySelector(".metadata-key").value;
                const type = item.querySelector(".metadata-type").value;
                if (key) metadata[key] = type;
            });

            let systemMetadata = {};

            // Select all checkboxes in the form
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name]');

            // Loop through checkboxes and collect the selected ones
            checkboxes.forEach((checkbox) => {
                if (checkbox.checked) {
                    const key = checkbox.name;
                    const type = checkbox.value;
                    if (key) systemMetadata[key] = type;
                }
            });

            // Create the final JSON object
            const data = {
                BucketName: bucketName,
                ListMetadataToBeIndexed: metadata,
                IsVersioning: isVersioning,
                SystemMetadata: systemMetadata
            };

            console.log(JSON.stringify(data));
            // Send data via fetch (adjust URL to your endpoint)
            fetch("http://localhost:8011/bucketmeta", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    // Check if the response status is not OK
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || "An error occurred.");
                        });
                    }
                    // Parse the response JSON if the request was successful
                    return response.json();
                })
                .then(result => {
                    alert("Success: " + JSON.stringify(result));
                    location.reload();
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Error: " + error.message);
                    location.reload();
                });

        }

        function addMetadata() {
            const container = document.getElementById("metadataContainer");
            const newMetadataItem = container.querySelector(".metadata-item").cloneNode(true);
            newMetadataItem.querySelector(".metadata-key").value = "";
            container.appendChild(newMetadataItem);
        }
    </script>
@endsection
