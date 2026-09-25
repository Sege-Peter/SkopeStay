<?php
$page_title = "Add New Room | SkopeStay";
require_once '../includes/config.php';
include '../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">

<?php include '../includes/sidebar.php'; ?>

<div class="md:ml-64 flex flex-col min-h-screen">
<?php include '../includes/header.php'; ?>

<main class="p-4 md:p-8 flex-grow space-y-6 max-w-5xl mx-auto w-full">

    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="rooms.php" class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-primary/10 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-bold text-2xl md:text-3xl text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">bed</span> Add New Room
            </h1>
            <p class="text-sm text-on-surface-variant mt-1">Fill in the room details below to add a new room to your property.</p>
        </div>
    </div>

    <form id="add-room-form" class="space-y-8" enctype="multipart/form-data">
        
        <!-- Basic Information -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-outline-variant/30 shadow-sm">
            <h2 class="text-lg font-bold text-on-surface mb-6 border-b border-outline-variant/30 pb-3">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Room Number *</label>
                    <input type="text" name="room_number" required placeholder="e.g. 101" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Room Name</label>
                    <input type="text" name="room_name" placeholder="e.g. Deluxe Room" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Room Type *</label>
                    <select name="type" required class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                        <option value="Standard Room">Standard Room</option>
                        <option value="Deluxe Room">Deluxe Room</option>
                        <option value="Executive Room">Executive Room</option>
                        <option value="Family Room">Family Room</option>
                        <option value="Junior Suite">Junior Suite</option>
                        <option value="Presidential Suite">Presidential Suite</option>
                        <option value="Honeymoon Suite">Honeymoon Suite</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Floor *</label>
                    <select name="floor" required class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                        <option value="0">Ground Floor</option>
                        <option value="1">First Floor</option>
                        <option value="2">Second Floor</option>
                        <option value="3">Third Floor</option>
                        <option value="4">Fourth Floor</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Occupancy Details -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-outline-variant/30 shadow-sm">
            <h2 class="text-lg font-bold text-on-surface mb-6 border-b border-outline-variant/30 pb-3">Occupancy Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Max Occupancy</label>
                    <input type="number" name="max_occupancy" value="2" min="1" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Adults Allowed</label>
                    <input type="number" name="adults_allowed" value="2" min="1" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Children Allowed</label>
                    <input type="number" name="children_allowed" value="0" min="0" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Bed Type</label>
                    <select name="bed_type" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                        <option value="Single Bed">Single Bed</option>
                        <option value="Double Bed">Double Bed</option>
                        <option value="Queen Bed">Queen Bed</option>
                        <option value="King Bed" selected>King Bed</option>
                        <option value="Twin Beds">Twin Beds</option>
                        <option value="Multiple Beds">Multiple Beds</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Number of Beds</label>
                    <input type="number" name="num_beds" value="1" min="1" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-outline-variant/30 shadow-sm">
            <h2 class="text-lg font-bold text-on-surface mb-6 border-b border-outline-variant/30 pb-3">Pricing</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Price Per Night (KSh) *</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-on-surface-variant">KSh</span>
                        <input type="number" name="price" required min="0" placeholder="8500" class="w-full pl-12 pr-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Weekend Rate (KSh)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-on-surface-variant">KSh</span>
                        <input type="number" name="weekend_rate" min="0" placeholder="Optional" class="w-full pl-12 pr-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Holiday Rate (KSh)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-on-surface-variant">KSh</span>
                        <input type="number" name="holiday_rate" min="0" placeholder="Optional" class="w-full pl-12 pr-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Discount Percentage (%)</label>
                    <div class="relative">
                        <input type="number" name="discount_percentage" min="0" max="100" placeholder="10" class="w-full pl-4 pr-10 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-on-surface-variant">%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Features & Description -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-outline-variant/30 shadow-sm">
            <h2 class="text-lg font-bold text-on-surface mb-6 border-b border-outline-variant/30 pb-3">Room Features & Description</h2>
            
            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-3">Select all applicable features:</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-8">
                <?php 
                $features = [
                    'Free WiFi', 'Air Conditioning', 'Smart TV', 'Mini Bar', 'Balcony', 
                    'Coffee Maker', 'Refrigerator', 'Work Desk', 'Room Service', 'Safe Box', 
                    'Hot Shower', 'Bathtub', 'Swimming Pool Access', 'Complimentary Breakfast', 
                    'Laundry Service', 'Ocean View', 'Garden View', 'Wheelchair Accessible'
                ];
                foreach($features as $f): 
                    $id = 'feat_'.md5($f);
                ?>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="features[]" value="<?= $f ?>" class="peer appearance-none w-5 h-5 border-2 border-outline-variant rounded bg-surface-container-low checked:bg-primary checked:border-primary transition-all">
                        <span class="material-symbols-outlined absolute text-white text-[16px] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none">check</span>
                    </div>
                    <span class="text-sm font-medium text-on-surface group-hover:text-primary transition-colors"><?= $f ?></span>
                </label>
                <?php endforeach; ?>
            </div>

            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Room Description</label>
            <textarea name="description" rows="5" placeholder="A spacious deluxe room featuring a king-size bed..." class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all resize-none"></textarea>
        </div>

        <!-- Room Images -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-outline-variant/30 shadow-sm">
            <h2 class="text-lg font-bold text-on-surface mb-6 border-b border-outline-variant/30 pb-3">Room Images</h2>
            
            <div id="drop-zone" class="border-2 border-dashed border-primary/40 bg-primary/5 rounded-2xl p-8 text-center cursor-pointer hover:bg-primary/10 transition-colors">
                <span class="material-symbols-outlined text-4xl text-primary mb-2">cloud_upload</span>
                <p class="font-bold text-primary mb-1">Click or drag images here to upload</p>
                <p class="text-xs text-on-surface-variant">Supported: JPG, PNG, WEBP (Max 5MB each)</p>
                <input type="file" id="image-input" name="room_images[]" multiple accept="image/jpeg, image/png, image/webp" class="hidden">
            </div>
            
            <div id="image-preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 empty:hidden">
                <!-- Previews generated by JS -->
            </div>
        </div>

        <!-- Settings & SEO -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Availability Settings -->
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-outline-variant/30 shadow-sm">
                <h2 class="text-lg font-bold text-on-surface mb-6 border-b border-outline-variant/30 pb-3">Availability & Display</h2>
                
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Status</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all mb-6">
                    <option value="Available">Available</option>
                    <option value="Occupied">Occupied</option>
                    <option value="Reserved">Reserved</option>
                    <option value="Under Maintenance">Under Maintenance</option>
                    <option value="Cleaning">Cleaning</option>
                </select>
                
                <div class="space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="display_on_website" value="1" checked class="peer appearance-none w-5 h-5 border-2 border-outline-variant rounded bg-surface-container-low checked:bg-primary checked:border-primary transition-all">
                            <span class="material-symbols-outlined absolute text-white text-[16px] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none">check</span>
                        </div>
                        <span class="text-sm font-medium text-on-surface group-hover:text-primary transition-colors">Display on Website</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="available_online" value="1" checked class="peer appearance-none w-5 h-5 border-2 border-outline-variant rounded bg-surface-container-low checked:bg-primary checked:border-primary transition-all">
                            <span class="material-symbols-outlined absolute text-white text-[16px] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none">check</span>
                        </div>
                        <span class="text-sm font-medium text-on-surface group-hover:text-primary transition-colors">Available for Online Booking</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="is_featured" value="1" class="peer appearance-none w-5 h-5 border-2 border-outline-variant rounded bg-surface-container-low checked:bg-primary checked:border-primary transition-all">
                            <span class="material-symbols-outlined absolute text-white text-[16px] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none">check</span>
                        </div>
                        <span class="text-sm font-medium text-on-surface group-hover:text-primary transition-colors">Featured Room</span>
                    </label>
                </div>

                <h3 class="text-sm font-bold text-on-surface mt-8 mb-4">Additional Services</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php 
                    $services = ['Airport Transfer', 'Spa Package', 'Lunch Package', 'Dinner Package', 'Conference Package', 'Pool Membership'];
                    foreach($services as $s): ?>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="additional_services[]" value="<?= $s ?>" class="peer appearance-none w-5 h-5 border-2 border-outline-variant rounded bg-surface-container-low checked:bg-primary checked:border-primary transition-all">
                            <span class="material-symbols-outlined absolute text-white text-[16px] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none">check</span>
                        </div>
                        <span class="text-sm font-medium text-on-surface group-hover:text-primary transition-colors"><?= $s ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- SEO & Website Display -->
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-outline-variant/30 shadow-sm">
                <h2 class="text-lg font-bold text-on-surface mb-6 border-b border-outline-variant/30 pb-3">SEO & Website Display</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Room Slug</label>
                        <input type="text" name="seo_slug" placeholder="e.g. deluxe-room-101" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                        <p class="text-[10px] text-on-surface-variant mt-1">Used for friendly URLs: skopestay.com/rooms/deluxe-room-101</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Meta Title</label>
                        <input type="text" name="meta_title" placeholder="SEO Title" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Meta Description</label>
                        <textarea name="meta_description" rows="3" placeholder="Brief description for search engines..." class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all resize-none"></textarea>
                    </div>
                </div>
            </div>

        </div>

        <!-- Actions Sticky Bar -->
        <div class="sticky bottom-4 z-50 bg-white/90 backdrop-blur-md rounded-2xl p-3 border border-outline-variant/30 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] flex flex-wrap justify-between items-center gap-3">
            <a href="rooms.php" class="px-4 py-2.5 rounded-xl font-bold text-sm text-error hover:bg-error-container transition-colors">Cancel</a>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="px-4 py-2.5 rounded-xl font-bold text-sm text-primary border border-primary hover:bg-primary/5 transition-colors">Preview Room</button>
                <button type="submit" name="action" value="save_another" class="px-4 py-2.5 rounded-xl font-bold text-sm text-on-surface bg-surface-container hover:bg-surface-container-high transition-colors">Save & Add Another</button>
                <button type="submit" name="action" value="save" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-primary shadow-lg hover:shadow-primary/50 hover:bg-primary/90 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span> Save Room
                </button>
            </div>
        </div>

    </form>
</main>

<?php include '../includes/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Drag and Drop Images Logic
    const dropZone = document.getElementById('drop-zone');
    const imageInput = document.getElementById('image-input');
    const previewContainer = document.getElementById('image-preview-container');
    let uploadedFiles = new DataTransfer();

    dropZone.addEventListener('click', () => imageInput.click());
    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('bg-primary/20'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('bg-primary/20'));
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('bg-primary/20');
        if (e.dataTransfer.files.length) {
            handleFiles(e.dataTransfer.files);
        }
    });
    imageInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if(file.type.startsWith('image/')) {
                uploadedFiles.items.add(file);
                createPreview(file, uploadedFiles.files.length - 1);
            }
        });
        imageInput.files = uploadedFiles.files; // Update actual input
    }

    function createPreview(file, index) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'relative rounded-xl overflow-hidden group aspect-square border border-outline-variant/30';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                    <button type="button" onclick="setPrimaryImage(this)" class="w-8 h-8 rounded-full bg-white text-primary flex items-center justify-center hover:bg-gray-100" title="Set Featured">
                        <span class="material-symbols-outlined text-[18px]">star</span>
                    </button>
                    <button type="button" onclick="removeImage(${index}, this)" class="w-8 h-8 rounded-full bg-error text-white flex items-center justify-center hover:bg-red-600" title="Remove">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
                <div class="primary-badge hidden absolute top-2 left-2 bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">Featured</div>
            `;
            previewContainer.appendChild(div);
            // Default first image to primary
            if(previewContainer.children.length === 1) {
                div.querySelector('.primary-badge').classList.remove('hidden');
                div.dataset.primary = '1';
            }
        };
        reader.readAsDataURL(file);
    }

    function removeImage(index, btn) {
        btn.closest('div.relative').remove();
        // We'd ideally reconstruct the DataTransfer object here to actually remove the file from form submission
        // For simplicity in UI, we just hide it, but robust implementation recreates the FileList.
        const dt = new DataTransfer();
        const { files } = imageInput;
        for (let i = 0; i < files.length; i++) {
            if (i !== index) dt.items.add(files[i]);
        }
        imageInput.files = dt.files;
        uploadedFiles = dt;
    }

    function setPrimaryImage(btn) {
        document.querySelectorAll('.primary-badge').forEach(b => {
            b.classList.add('hidden');
            b.closest('div.relative').dataset.primary = '0';
        });
        const container = btn.closest('div.relative');
        container.querySelector('.primary-badge').classList.remove('hidden');
        container.dataset.primary = '1';
        
        // Mark input hidden field for primary image index
        const index = Array.from(previewContainer.children).indexOf(container);
        let hiddenInput = document.getElementById('primary-image-index');
        if(!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.id = 'primary-image-index';
            hiddenInput.name = 'primary_image_index';
            document.getElementById('add-room-form').appendChild(hiddenInput);
        }
        hiddenInput.value = index;
    }

    // Form Submission
    document.getElementById('add-room-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Check which submit button was clicked
        const actionType = e.submitter.value; 

        Swal.fire({
            title: 'Saving Room...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const res = await fetch('../api/rooms.php', {
                method: 'POST',
                body: formData
            });
            const json = await res.json();
            
            if (json.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: json.message,
                    confirmButtonColor: 'var(--color-primary)'
                }).then(() => {
                    if(actionType === 'save_another') {
                        location.reload();
                    } else {
                        window.location.href = 'rooms.php';
                    }
                });
            } else {
                Swal.fire('Error', json.message, 'error');
            }
        } catch(err) {
            Swal.fire('Error', 'Failed to connect to the server.', 'error');
        }
    });

    // Auto-generate slug from Room Name
    document.querySelector('input[name="room_name"]').addEventListener('input', function() {
        const slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        const roomNum = document.querySelector('input[name="room_number"]').value;
        document.querySelector('input[name="seo_slug"]').value = slug ? `${slug}-${roomNum}` : '';
    });
</script>
</body>
</html>
