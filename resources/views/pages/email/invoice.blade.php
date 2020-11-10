<table cellspacing="0" cellpadding="0" align="center" style="width: 650px; margin: 0 auto; font-family: arial; border:1px solid #e2e2e2">
	<tbody>
		<tr>
		<td style="font-family:'Roboto',Arial,Helvetica; width: 40%; min-width:40%; padding: 5px; border-right:1px solid #e2e2e2;">
				<a href="#"><img src="{{ URL::asset('/media') }}/marine.png" alt="Marine"></a>
			</td>
			<td style="text-align: center; width: 30%; padding: 5px; border-right:1px solid #e2e2e2;">
			<h5 style="font-size: 30px; margin: 0px 0px;">Invoice</h5>
				<p style="margin: 0px; color:red;">Ref. #: {{$data1['content']['survey_number']}}</p>
			</td>
			<td style="text-align: right; width: 33.33%; padding: 5px;">
				<a href="#"><img src="{{ URL::asset('/media') }}/invoice-logo.png" alt="Marine"></a>
			</td>
		</tr>
		<tr>
			<td style="padding: 5px; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;">Date: <span>{{$data1['content']['date']}}</span></td>
			<td style="padding: 5px; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;text-align:center;">Amount (USD): <span>{{$data1['content']['amount']}}</span></td>
			<td style="padding: 5px; border-top: 1px solid #e2e2e2; text-align: right;">Due Date: <span>{{$data1['content']['due_date']}}</span></td>
		</tr>
		<tr>
			<td style="vertical-align: top; padding: 5px; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;">
				<strong>From:</strong>
				<p style="font-size: 14px; margin-top: 5px;">MARINE INFOTECH LLC
					<span style="display: block; margin-bottom: 3px;">9494 SOUTHWEST FWY STE 720</span>
					<span style="display: block; margin-bottom: 3px;">HOUSTON, TX USA 77477</span>
					<span style="display: block; margin-bottom: 3px;"><a href="#" style="color: blue; text-decoration: none;">imars@marineinfotech.com</a></span></p>
			</td>
			<td colspan="2" style="vertical-align: top; padding: 5px; border-top: 1px solid #e2e2e2;">
				<strong>To:</strong>
				<p style="font-size: 14px; margin-top: 5px;">{{$data1['content']['to']['company']}}
					<span style="display: block; margin-bottom: 3px;word-break: break-all;">Attn: {{$data1['content']['to']['operator_name']}}</span>
					<span style="display: block; margin-bottom: 3px;word-break: break-all;">{{$data1['content']['to']['address1']}}</span>
					<span style="display: block; margin-bottom: 3px;word-break: break-all;">{{$data1['content']['to']['address2']}}</span>
					<span style="display: block; margin-bottom: 3px;word-break: break-all;">{{$data1['content']['to']['email']}}</span>
				</p>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<table style="width: 100%;" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td style="text-align: center;padding: 10px; border-top: 1px solid #e2e2e2;"><strong>SURVEY DETAILS</strong></td>
						</tr>
						<tr>
							<td>
								<table cellspacing="0" cellpadding="0" width="100%">
									<thead>
										<tr>
											<th style="border-top: 1px solid #e2e2e2;padding: 5px;border-right: 1px solid #e2e2e2;">SURVEY #</th>
											<th style="border-top: 1px solid #e2e2e2;padding: 5px;border-right: 1px solid #e2e2e2;">VESSEL NAME</th>
											<th style="border-top: 1px solid #e2e2e2;padding: 5px;border-right: 1px solid #e2e2e2;">IMO #</th>
											<th style="border-top: 1px solid #e2e2e2;padding: 5px;">PORT NAME</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="text-align: center;padding: 15px 5px; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;">{{$data1['content']['survey_number']}}</td>
											<td style="text-align: center;padding: 15px 5px; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;">{{$data1['content']['vesselsname']}}</td>
											<td style="text-align: center;padding: 15px 5px; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;">{{$data1['content']['imo_number']}}</td>
											<td style="text-align: center;padding: 15px 5px; border-top: 1px solid #e2e2e2;">{{$data1['content']['port_name']}}</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style="height: 30px; border-top:1px solid #e2e2e2;"></td>
						</tr>
						<tr>
							<td>
								<table cellspacing="0" cellpadding="0" width="100%">
									<thead>
										<tr>
											<th style="border-top: 1px solid #e2e2e2;padding: 5px;border-right: 1px solid #e2e2e2;">Description</th>
											<th style="border-top: 1px solid #e2e2e2;padding: 5px;">Amount</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="text-align: left;padding: 5px; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;"><?php echo strtoupper($data1['content']['survey_type_name']);?> </td>
											<td style="text-align: center;padding: 5px; border-top: 1px solid #e2e2e2;">USD {{$data1['content']['survey_type_price']}}</td>
										</tr>
										<tr>
											<td style="text-align: left;padding: 5px; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;">TRANSPORTATION COST </td>
											<td style="text-align: center;padding: 5px; border-top: 1px solid #e2e2e2;">USD {{$data1['content']['port_price']}}</td>
										</tr>
										<tr>
											<td style="text-align: left;padding: 5px; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;"><strong>INVOICE TOTAL AMOUNT DUE </strong> </td>
											<td style="text-align: center;padding: 5px; border-top: 1px solid #e2e2e2;"><strong style="color: red;">USD {{$data1['content']['amount']}}</strong></td>
										</tr>
										<tr>
											<td style="text-align: left;padding: 5px; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2;"><strong>INVOICE TOTAL AMOUNT DUE DATE</strong></td>
											<td style="text-align: center;padding: 5px; border-top: 1px solid #e2e2e2;">
												<strong style="color: red;">{{$data1['content']['due_date']}}</strong>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style="height: 30px; border-top:1px solid #e2e2e2;"></td>
						</tr>
						<tr>
							<td>
								<table cellspacing="0" cellpadding="0" width="100%">
									<tbody>
										<tr>
											<td style="text-align: center; font-weight: bold; padding: 10px; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2; border-bottom: 1px solid #e2e2e2;">PAY INVOICE TOTAL AMOUNT TO:</td>
										</tr>
										<tr>
											<td>
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td style="padding: 8px; width: 30%; border-right:1px solid #e2e2e2; border-bottom: 1px solid #e2e2e2;">NAME</td>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2;">MARINE INFOTECH LLC</td>
													</tr>
													<tr>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2; width: 30%; border-right:1px solid #e2e2e2;">ADDRESS</td>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2;">9494 SOUTHWEST FWY STE 720 HOUSTON, TX USA 77477</td>
													</tr>
													<tr>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2; width: 30%; border-right:1px solid #e2e2e2;">EMAIL</td>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2;">imars@marineinfotech.com</td>
													</tr>
													<tr>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2; width: 30%; border-right:1px solid #e2e2e2;">BANK NAME</td>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2;">JP MORGAN CHASE BANK, NA NEWYORK</td>
													</tr>
													<tr>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2; width: 30%; border-right:1px solid #e2e2e2;">BRANCH NAME</td>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2;">HWY 6 AND W AIRPORT</td>
													</tr>
													<tr>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2; width: 30%; border-right:1px solid #e2e2e2;">SWIFT CODE</td>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2;">CHASUS33 (or CHASUS33XXX)</td>
													</tr>
													<tr>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2; width: 30%; border-right:1px solid #e2e2e2;">ROUTING NUMBER</td>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2;">111000614</td>
													</tr>
													<tr>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2; width: 30%; border-right:1px solid #e2e2e2;">ACCOUNT NUMBER</td>
														<td style="padding: 8px;border-bottom: 1px solid #e2e2e2;">306115830</td>
													</tr>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style="text-align: center; padding: 10px 5px; color: red; font-weight: bold;">
								FOR ALL WIRE TRANSFERS, BE SURE TO LIST ALL THE INVOICE REFERENCE NUMBERS THAT ARE INCLUDED IN THE PAYMENT.
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
