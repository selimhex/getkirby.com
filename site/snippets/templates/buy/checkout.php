<dialog class="dialog checkout" @click="closeCheckout">
	<form class="dialog-form" action="<?= url('buy') ?>" method="POST" target="_blank" @submit="cachePersonalInfo">
		<div class="checkout-preview">
			<div>
				<h2 class="label">Your order</h2>
				<table>
					<tr>
						<th>
							<div class="inputs">
								<input type="number" name="quantity" value="1" required min="1" max="100" step="1" v-model="quantity" @input="restrictQuantity">
								<select required name="product" v-model="product" value="<?= $basic->value() ?>">
									<option value="<?= $basic->value() ?>" selected>Kirby <?= $basic->label() ?></option>
									<option value="<?= $enterprise->value() ?>">Kirby <?= $enterprise->label() ?></option>
								</select>
							</div>
						</th>
						<td>{{ amount(netLicenseAmount) }}</td>
					</tr>
					<tr v-if="discountRate">
						<th>
							<p>Volume Discount (-{{ discountRate }}%)</p>
							<p v-if="quantity >= 30" class="text-xs color-gray-700">Please <a class="underline" href="mailto:support@getkirby.com">contact us</a> for <span class="whitespace-nowrap">high-volume</span> discounts</p>
						</th>
						<td>{{ amount(discountAmount) }}</td>
					</tr>
					<tr v-if="personalInfo.donate">
						<th>
							Your donation
						</th>
						<td>{{ amount(donationAmount) }}</td>
					</tr>
					<tr class="subtotal" v-if="vatRate > 0">
						<th>
							Subtotal
						</th>
						<td>{{ amount(subtotal) }}</td>
					</tr>
					<tr v-if="vatRate > 0">
						<th>
							VAT ({{ vatIdExists ? 0 : vatRate }}%)
						</th>
						<td>{{ amount(vatAmount) }}</td>
					</tr>
					<tr class="total">
						<th>
							Total
						</th>
						<td>{{ amount(totalAmount) }}</td>
					</tr>
				</table>
			</div>

			<?php if ($donation['customerAmount'] > 0): ?>
			<div>
				<h2 for="donate" class="font-bold">Support a good cause</h2>
				<p class="mb-3">
					For every purchased license we donate <span class="whitespace-nowrap">€<?= $donation['teamAmount'] ?></span><span class="whitespace-nowrap" v-if="currencySign !== '€'" v-text="' (~ ' + currencySign + prices.donation.team + ')'"></span> to
					<a class="link" rel="noopener noreferrer" target="_blank" href="<?= $donation['link'] ?>"><?= $donation['charity'] ?></a> <?= $donation['purpose'] ?>.
				</p>
				<label class="checkbox">
					<input id="donate" type="checkbox" name="donate" v-model="personalInfo.donate">
					<span v-text="donationText">Donate an additional €<?= $donation['customerAmount'] ?> per license 💛</span>
				</label>
			</div>
			<?php endif ?>
		</div>
		<div class="checkout-form">
			<div class="field">
				<label class="label" for="email">Email <abbr title="Required">*</abbr></label>
				<input id="email" name="email" class="input" type="email" required v-model="personalInfo.email" placeholder="mail@example.com">
			</div>
			<div class="field">
				<label class="label" for="country">Country <abbr title="Required">*</abbr></label>
				<select id="country" name="country" required autocomplete="country" class="input" v-model="country">
					<?php foreach ($countries as $countryCode => $countryName): ?>
					<option value="<?= $countryCode ?>"><?= $countryName ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div v-if="needsPostalCode" class="field">
				<label class="label" for="postalCode">Postal Code <abbr title="Required">*</abbr></label>
				<input id="postalCode" name="postalCode" class="input" autocomplete="postal-code" :required="needsPostalCode" v-model="personalInfo.postalCode" type="text">
			</div>
			<div class="field">
				<label class="label" for="vatId">VAT ID</label>
				<input id="vatId" name="vatId" class="input" type="text" v-model="personalInfo.vatId">
				<p v-if="vatIdExists" class="color-gray-700 text-xs pt-1">Your VAT ID will be validated on checkout</p>
			</div>

			<fieldset v-if="vatIdExists">
				<div class="field">
					<label class="label" for="company">Company Name <abbr title="Required">*</abbr></label>
					<input id="company" name="company" autocomplete="organization" class="input" type="text" v-model="personalInfo.company" :required="vatIdExists">
				</div>

				<div class="field">
					<label class="label" for="street">Street <abbr title="Required">*</abbr></label>
					<input id="street" name="street" class="input" type="text" v-model="personalInfo.street" :required="vatIdExists">
				</div>

				<div class="field">
					<label class="label" for="city">Town/City <abbr title="Required">*</abbr></label>
					<input id="city" name="city" class="input" type="text" v-model="personalInfo.city" :required="vatIdExists">
				</div>

				<div class="field">
					<label class="label" for="state">State/County <abbr title="Required">*</abbr></label>
					<input id="state" name="state" class="input" type="text" v-model="personalInfo.state" :required="vatIdExists">
				</div>
			</fieldset>

			<div class="field">
				<label class="label" for="newsletter">Newsletter</label>
				<label class="checkbox">
					<input id="newsletter" type="checkbox" name="newsletter" v-model="personalInfo.newsletter">
					Subscribe to our Kosmos newsletter
				</label>
				<p class="color-gray-700 text-xs pt-1">We won't ever spam you! You can unsubscribe at any time. <a class="underline" target="_blank" href="<?= url('kosmos') ?>">Learn more about Kosmos…</a></p>
			</div>

			<div class="buttons">
				<button type="submit" class="btn btn--filled"><?= icon('cart') ?> Checkout</button>
			</div>
		</div>
	</form>
</dialog>

<style>
@import url("/assets/css/site/dialog.css");

@media screen and (min-width: 40rem) {
	.checkout form {
		display: grid;
		grid-template-columns: 1fr 1fr;
		grid-template-areas: "preview form";
	}
}

.checkout-preview {
	grid-area: preview;
	background: var(--color-white);
}

.checkout-form {
	grid-area: form;
	padding: var(--spacing-8);
}

.checkout fieldset {
	margin-block: var(--spacing-6);
}
.checkout .buttons {
	margin-top: var(--spacing-8);
	display: flex;
	gap: .75rem;
}
.checkout .buttons .btn {
	flex-basis: 50%;
	flex-grow: 1;
}
.checkout-preview {
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	padding: var(--spacing-8);
}
.checkout-preview :where(th, td) {
	border-top: 1px solid var(--color-border);
	padding-block: var(--spacing-2);
}
.checkout-preview th {
	font-weight: var(--font-normal);
}
.checkout-preview th .inputs {
	display: flex;
	gap: .25rem;
	align-items: center;
}
.checkout-preview th :where(input, select) {
	background: var(--color-light);
	height: 1.25rem;
	line-height: 1.25;
	padding-inline: var(--spacing-1);
	border-radius: var(--rounded);
}
.checkout-preview th input {
	width: 3rem;
}
.checkout-preview td {
	text-align: right;
}
.checkout-preview :where(tr.total, tr.subtotal) > * {
	border-top-width: 2px;
}
.checkout-preview tr.total > * {
	font-weight: var(--font-bold);
}
</style>
