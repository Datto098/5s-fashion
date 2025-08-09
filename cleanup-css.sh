#!/bin/bash

# Files to process
files=(
    "public/assets/css/homepage.css"
    "public/assets/css/cart.css"
    "public/assets/css/product-detail.css"
    "public/assets/css/checkout.css"
    "public/assets/css/auth.css"
    "public/assets/css/order-success.css"
    "public/assets/css/order-success-v2.css"
    "public/assets/css/order-tracking.css"
    "public/assets/css/shop.css"
    "public/assets/css/chatbot.css"
    "public/assets/css/admin.css"
    "public/assets/css/admin-sidebar.css"
    "public/assets/css/admin-complete.css"
    "public/assets/css/checkout-validation.css"
    "public/assets/css/bank-transfer.css"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "Processing: $file"

        # Remove linear gradients - replace with solid colors (using first color in gradient)
        sed -i 's/background: linear-gradient([^;]*#[a-fA-F0-9]\{3,6\}[^;]*);/background: #007bff;/g' "$file"
        sed -i 's/background: linear-gradient([^;]*var([^)]*)[^;]*);/background: var(--primary-color);/g' "$file"

        # Remove transform hover effects
        sed -i '/transform: translateY/d' "$file"
        sed -i '/transform: translateX/d' "$file"
        sed -i '/transform: scale/d' "$file"
        sed -i '/transform: translate3d/d' "$file"
        sed -i '/transform: scale3d/d' "$file"

        echo "Processed: $file"
    fi
done

echo "All files processed!"
