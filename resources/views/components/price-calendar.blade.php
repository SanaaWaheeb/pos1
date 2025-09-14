@php
    $today = now();
    $month = (int) request('month', $today->month);
    $year = (int) request('year', $today->year);
    $monthName = date('F', mktime(0, 0, 0, $month, 10));
@endphp

@props([
    'readOnly' => true,
])

<div id="calendar-container" class="price-calendar border p-3 rounded" data-month="{{ $month }}" data-year="{{ $year }}">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button id="prev-month" class="btn btn-outline-primary">&lt;</button>
        <span class="align-self-center fw-bold" id="month-year-label">{{ $monthName }} {{ $year }}</span>
        <button id="next-month" class="btn btn-outline-primary">&gt;</button>
    </div>

    <div id="calendar-grid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px;"></div>
</div>

<style>
    .price-calendar input[type="number"] {
        font-size: 12px;
        padding: 2px;
    }
    .price-calendar input[disabled] {
        background-color: #eaeaea;
        cursor: not-allowed;
    }
    .selected-start,
    .selected-end {
        background-color: var(--theme-color) !important;
        color: var(--white);
    }

    .selected-start input[type="number"],
    .selected-end input[type="number"] {
        color: var(--grey-color);
    }

    .in-range {
        background-color: var(--grey-color) !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const readOnly = @json($readOnly);
    const slug = "{{ $store->slug ?? '' }}";
    let existingPrices = @json($productPrices ?? []); // Fetch existing prices from DB for a single product (edit product page)
    let selectedStartDate = null;
    let selectedEndDate = null;

    function formatPrice(price) {
        const decimalPlaces = 2;
        const currency = "{{ $store->currency ?? '' }}";
        const currencySymbolPosition = "{{ $store->currency_symbol_position ?? '' }}";
        const currencySymbolSpace = "{{ $store->currency_symbol_space ?? '' }}";

        // Ensure price is formatted with the correct decimal places
        let formattedPrice = price.toFixed(decimalPlaces);
        // Determine the space between currency and price
        let space = currencySymbolSpace === "with" ? " " : "";

        // Format price based on the currency position
        if (currencySymbolPosition === "pre") {
            return currency + space + formattedPrice;
        } else if (currencySymbolPosition === "post") {
            return formattedPrice + space + currency;
        }
        // Default fallback (if no valid position is set)
        return currency + formattedPrice;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('calendar-container');
        const grid = document.getElementById('calendar-grid');
        const label = document.getElementById('month-year-label');
        const prevBtn = document.getElementById('prev-month');
        const nextBtn = document.getElementById('next-month');
        const defaultInput = document.getElementById('default-price-input');

        let defaultPrice = parseFloat(defaultInput?.value || 0);
        let priceMap = { ...existingPrices }; // Tracks manually edited prices: prefill with existing DB values

        const renderCalendarGrid = (month, year) => {
            const firstDay = new Date(year, month - 1, 1);
            const firstWeekDay = (firstDay.getDay() + 6) % 7 + 1;
            const daysInMonth = new Date(year, month, 0).getDate();

            let html = '';
            const weekDays = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];
            weekDays.forEach(day => {
                html += `<div class="text-center fw-bold border-bottom pb-1">${day}</div>`;
            });

            for (let i = 1; i < firstWeekDay; i++) {
                html += `<div></div>`;
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year.toString().padStart(4, '0')}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                const value = priceMap[dateStr] ?? defaultPrice;

                // Disable if date is before today
                const today = new Date();
                const inputDate = new Date(`${dateStr}T00:00:00`);
                const isPast = inputDate < new Date(today.getFullYear(), today.getMonth(), today.getDate());

                let cellClass = '';
                // Allow select start and end date in readOnly mode
                if (readOnly) {
                    if (selectedStartDate && selectedEndDate) {
                        const inRange = inputDate > new Date(selectedStartDate) && inputDate < new Date(selectedEndDate);
                        if (inRange) cellClass = 'in-range';
                    }
                    if (dateStr === selectedStartDate) cellClass = 'selected-start';
                    if (dateStr === selectedEndDate) cellClass = 'selected-end';
                }

                html += `
                    <div class="text-center border rounded p-1 bg-light date-cell ${cellClass}" data-date="${dateStr}" style="${readOnly ? 'cursor: pointer;' : ''}">
                        <div class="fw-bold">${day}</div>
                        <input type="number" name="daily_prices[${dateStr}]"
                            class="form-control form-control-sm mt-1 text-center price-input"
                            data-date="${dateStr}"
                            value="${value}"
                            step="any"
                            ${isPast ? 'disabled' : ''} />
                    </div>
                `;
            }

            return html;
        };

        const attachInputListeners = () => {
            document.querySelectorAll('.price-input').forEach(input => {
                input.addEventListener('input', function () {
                    const date = this.dataset.date;
                    const val = this.value;
                    if (val !== '') {
                        priceMap[date] = parseFloat(val);
                    } else {
                        delete priceMap[date];
                    }
                });
            });
        };

        const loadCalendar = (month, year) => {
            grid.innerHTML = renderCalendarGrid(month, year);
            container.dataset.month = month;
            container.dataset.year = year;

            const newDate = new Date(year, month - 1);
            const monthName = newDate.toLocaleString('default', { month: 'long' });
            label.textContent = `${monthName} ${year}`;

            attachInputListeners();

            document.querySelectorAll('.date-cell').forEach(cell => {
                const date = cell.dataset.date;
                const isPast = new Date(`${date}T00:00:00`) < new Date(new Date().toDateString());

                if (readOnly && !isPast) {
                    cell.addEventListener('click', () => {
                        if (
                            !selectedStartDate 
                            || (selectedStartDate && selectedEndDate)
                            || (new Date(date) < new Date(selectedStartDate))
                        ) {
                            selectedStartDate = date;
                            $('#check-in-date').val(date);
                            document.getElementById("check-in").textContent = date;
                            $('#check-out-date').val(null);
                            document.getElementById("check-out").textContent = null;
                            selectedEndDate = null;
                            $('#number_of_nights').val(1);
                            document.getElementById("num-nights").textContent = "{{ __('Night') }}";
                            $('#total-booking-price').val(null); // Update hidden input field
                            $('.pro_total_price').html('');
                            $('.pro_total_price').attr('data-value', 0);

                        } else {
                            selectedEndDate = date;
                            $('#check-out-date').val(date);
                            document.getElementById("check-out").textContent = date;
                        }

                        // Calculate number of nights
                        if (selectedStartDate && selectedEndDate) {
                            const start = new Date(selectedStartDate);
                            const end = new Date(selectedEndDate);
                            const diffDate = end - start;
                            const nights = Math.ceil(diffDate / (1000 * 60 * 60 * 24));
                            let totalPrice = 0;

                            $('#number_of_nights').val(nights);
                            document.getElementById("num-nights").textContent = nights + ' ' + (nights > 1 ? "{{ __('Nights') }}" : "{{ __('Night') }}");

                            // Step 2: Loop through all visible inputs in the calendar
                            document.querySelectorAll('.price-input').forEach(input => {
                                const dateStr = input.dataset.date;
                                if (!dateStr) return;

                                const currentDate = new Date(`${dateStr}T00:00:00`);
                                
                                // Step 3: Check if this date is in the selected range
                                if (currentDate >= start && currentDate <= end) {
                                    const val = parseFloat(input.value || 0);
                                    if (!isNaN(val)) totalPrice += val;
                                }
                            });

                            // Step 4: reduce total price by coupon amount if applied
                            $('.pro_total_price').attr('data-value', formatPrice(totalPrice));
                            const coupon = $('.apply-coupon').closest('.row').find('.coupon').val();
                            if (coupon != "") {
                                $('.apply-coupon').trigger('click');
                            }

                            // Step 5: Display or use the total
                            $('#total-booking-price').val(formatPrice(totalPrice)); // Update hidden input field
                            $('.pro_total_price').html(formatPrice(totalPrice));

                        }
                        loadCalendar(parseInt(container.dataset.month), parseInt(container.dataset.year));
                    })
                }
            })
        };

        // Fetch prices for readOnly calendar
        if (readOnly && slug) {
            $.ajax({
                url: "{{ route('product.prices') }}",
                type: 'GET',
                data: {
                    slug,
                },
                headers: {
                    'x-csrf-token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    response.calendar_prices.forEach(entry => {
                        const date = Object.keys(entry)[0];
                        priceMap[date] = entry[date];
                    });
                    defaultPrice = response.default_total;
                    loadCalendar(parseInt(container.dataset.month), parseInt(container.dataset.year));
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert("{{ __('An error occurred. Please try again later.') }}");
                }
            });
        }

        prevBtn.addEventListener('click', e => {
            const today = new Date();
            const currentYear = today.getFullYear();
            const currentMonth = today.getMonth()+1;
            e.preventDefault();
            let m = parseInt(container.dataset.month) - 1;
            let y = parseInt(container.dataset.year);
            const isPast = (y < currentYear) || (y === currentYear && m < currentMonth);
            if (!isPast) {
                if (m < 1) { m = 12; y--; }
                loadCalendar(m, y);
            }
        });

        nextBtn.addEventListener('click', e => {
            e.preventDefault();
            let m = parseInt(container.dataset.month) + 1;
            let y = parseInt(container.dataset.year);
            if (m > 12) { m = 1; y++; }
            loadCalendar(m, y);
        });

        if (!readOnly) {
            defaultInput.addEventListener('input', function () {
                defaultPrice = parseFloat(this.value || 0);
                document.querySelectorAll('.price-input').forEach(input => {
                    const date = input.dataset.date;
                    if (!priceMap[date] || input.value === '' || input.value == defaultPrice) {
                        input.value = defaultPrice;
                    }
                });
            });
        }

        loadCalendar(parseInt(container.dataset.month), parseInt(container.dataset.year));
    });
</script>