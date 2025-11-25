<script setup>

</script>

<template>

	<div class="menu-item pt-5">
		<div class="menu-content">
			<span class="menu-heading fw-bold text-uppercase fs-7">E-TİCARET YÖNETİMİ</span>
		</div>
	</div>

	<div class="menu-item">
		<Link class="menu-link" :href="route('customers.index')" :only="['flash','users']">
			<span class="menu-icon"><i class="bi bi-person fs-2"></i></span>
			<span class="menu-title">Üye Listesi</span>
		</Link>
	</div>
	
	<div class="menu-item">
		<Link class="menu-link" :href="route('orders.index')" :only="['flash','orders']">
			<span class="menu-icon"><i class="bi bi-cart fs-2"></i></span>
			<span class="menu-title">Siparişler</span>
		</Link>
	</div>

	<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
		<span class="menu-link">
			<span class="menu-icon"><i class="bi bi-tag fs-2"></i></span>
			<span class="menu-title">Kupon Yönetimi</span>
			<span class="menu-arrow"></span>
		</span>
		<div class="menu-sub menu-sub-accordion">
			<div class="menu-item p-0">
				<Link class="menu-link ps-10 py-2" :href="route('coupon-groups.index')" :only="['flash','coupon-groups']">
					<span class="menu-title">Gruplar</span>
				</Link>
			</div>
			<div class="menu-item p-0">
				<Link class="menu-link ps-10 py-2" :href="route('coupons.index')" :only="['flash','coupons']">
					<span class="menu-title">Kuponlar</span>
				</Link>
			</div>
			<div class="h-10px"></div>
		</div>
	</div>

	<div class="menu-item">
		<Link class="menu-link" :href="route('campaigns.index',{language:$page.props.current_language})" :only="['flash', 'campaigns']">
			<span class="menu-icon"><i class="bi bi-gift fs-2"></i></span>
			<span class="menu-title">Kampanyalar</span>
		</Link>
	</div>

	<div class="menu-item">
		<Link class="menu-link" :href="route('announcements.index',{language:$page.props.current_language})" :only="['flash', 'announcements']">
			<span class="menu-icon"><i class="bi bi-megaphone fs-2"></i></span>
			<span class="menu-title">Duyurular</span>
		</Link>
	</div>

	<div class="menu-item pt-5">
		<div class="menu-content">
			<span class="menu-heading fw-bold text-uppercase fs-7">ÜRÜN YÖNETİMİ</span>
		</div>
	</div>

	<div data-kt-menu-trigger="click" class="menu-item menu-accordion" v-for="(type,s) in $page.props.productTypes" :key="s">
		<span class="menu-link">
			<span class="menu-icon"><i class="bi bi-back fs-2"></i></span>
			<span class="menu-title">{{ type.name }}</span>
			<span class="menu-arrow"></span>
		</span>
		<div class="menu-sub menu-sub-accordion">
			<div class="menu-item p-0">
				<Link class="menu-link ps-10 py-2" :href="route('products.index',{productType:type.id,language:$page.props.current_language})">
					<span class="menu-title">Ürün Listesi</span>
				</Link>
			</div>
			<div class="menu-item p-0">
				<Link class="menu-link ps-10 py-2" :href="route('product-categories.index',{productType:type.id,language:$page.props.current_language})">
					<span class="menu-title">Kategoriler</span>
				</Link>
			</div>
			<div class="menu-item p-0" v-if="type.is_cartable">
				<Link class="menu-link ps-10 py-2" :href="route('product-types.sales',type.id)">
					<span class="menu-title">Satış Raporları</span>
				</Link>
			</div>
			<div class="h-10px"></div>
		</div>
	</div>

</template>