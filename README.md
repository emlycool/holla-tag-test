###  Problem
An application for billing 10,000 users over a given billing API (owned by a third party e.g. Telco/Bank). 
-  The billing API takes 1.6secs to process and respond to each request 			  		
- The details of the users to bill is stored in a Database with fields; id, username, mobile_number and amount_to_bill

### Requirements: 
Write or describe a simple PHP code that will be able bill all the 10,000 users within 1hr.
 Also suggest an approach to scale the above if you need to bill 100,000 users within 4.5hrs

#### Things to consider
##### Issue 1
- Memory issue( loading large amount(100,000) of records(users to bill)).
  -  <b>Solution</b> Using php generators/ laravel lazy collection can help otimize memory when dealing with large records
##### Issue 2
-  <b> Scaling</b> is constraint to the rate limit of the third party api for billing.
	-  <b>Solution </b> the proposed solution, is using async programming to send multiple non blocking request  at a time. Luckily the libary <a href="https://docs.guzzlephp.org/en/stable/quickstart.html"> Guzzle</a> offers ablity to send concurrent requests. So this can be use to integrate with the third party api. <br>
You can find example of a job to bill users async requests in <a href="https://github.com/emlycool/holla-tags-test/blob/master/app/Jobs/BillUsersJob.php"> BillUserJob</a> handle() method. <br>
By sending 5 requests at a time non-blocking reach other.
10,000 requests should complete in approximately 53 minutes (1.6 *10,000 /5).
Also running such a time consuming task, should be run in background(queue) and or as a cron job using the php cli and not through a web request.
I have create a command to run the BillUserJob. which can be found in `App\Console\Commands\BillCommand` to run `php artisan bill:users`.
In a real application to make need to schedule that to cron job to run at desired billing period.
 

##### Problem 2: Scaling for 100,000 users to bill within 4.5 hours.
Billing user synchronously (one at a time) will take approximately 44.44 hours
To bill 100,000 users in 4.5 hours will require it to be 10X faster 
(44.44 / 4.5 = 9.88). 
Sending 10 concurrent request at a time. You may need to have more than one billing api third party service, depedending on the rate limit of the third part api.

### Setup instruction to test
`git clone`
`composer install`
`php artisan migrate --seed`
`php artisan bill:users`
Please note integration with billing api isnt implemented. Just mock out using `POST` request of user data to a particular endpoint.
 
