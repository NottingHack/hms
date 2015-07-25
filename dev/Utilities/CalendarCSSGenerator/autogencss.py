"""
autogencss.py

Automatic generation of CSS for showing tool bookings.

Usage: python3 autogencss.py > "view.css"
"""

# Set pixel heights for small time intervals
small_heights = {
	15:4,
	30:8,
	45:16,
}

def print_fixed_css():
	"""
	Grab the 'fixed' CSS and just echo it back out
	"""

	for line in open("fixed.css"):
		print(line, end="")
	print("")

def generate_duration_css():
	"""
	Generate div entries for time intervals between 15 minutes
	and 240 minutes in 15 minute intervals
	"""

	current_height = 23 # Start at 23px for a 60 minute slot

	for duration in range(15, 241, 15):
		print("div.len_{0} {{".format(duration))

		try:
			# Try using the duration in the small_heights dictionary
			print("\theight: {0}px;".format(small_heights[duration]))
			print("\tfont-size: 0.7em;")
		except:
			print("\theight: {0}px;".format(current_height))
			## After 60 minutes, alternate adding 8px then 7px to height
			current_height += 7
			if duration % 30 == 0:
				current_height += 1
			
		print("}")
		print("")
		
def generate_time_css():
	"""
	Generate div entries for each 15 minute interval during a 24 hour period
	"""

	top = 33 ## Start at 33px and work down the calendar

	for hh in range(0, 24):
		for mm in range(0, 60, 15):
			print("div.start_{0:02d}{1:02d} {{".format(hh,mm))
			print("\ttop: {0}px;".format(top))
			print("}")
			print("")
			top += 7
			if mm % 30:
				top += 1

if __name__ == "__main__":
	print_fixed_css()
	generate_duration_css()
	generate_time_css()
