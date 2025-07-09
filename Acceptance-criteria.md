Acceptance criteria
Using any programming language and libraries you would like:
	1.	An HTTP server is built to handle tree data structures. The server will expose two endpoints:
	a.	GET /api/tree - Returns an array of all trees that exist in the database
	b.	POST /api/tree - Creates a new node and attaches it to the specified parent node in the tree
	2.	A persistence layer is implemented within the HTTP server, e.g data is retained between server starts/stops
	3.	Testing is implemented ensure the server is behaving as expected
Hint: complete this challenge as a production API made available to other developers.

Specifications
GET /api/tree
Example Response: 
[
    {
        "id": 1,
        "label": "root",
        "children": [
            {
                "id": 3,
                "label": "bear",
                "children": [
                    {
                        "id": 4,
                        "label": "cat",
                        "children": []
                    },
                ]
            },
            {
                "id": 7,
                "label": "frog",
                "children": []
            }
        ]
    }
]


POST /api/tree
Example request body:
{
  "label": "cat’s child",  "parentId": 4}

Submission
Submit your code via GitHub repository or any platform that allows code sharing. Include a README file with instructions on how to run your server and test the endpoints. Email minh.nguyen@theary.com and blake.dong@theary.com with the link to your repository.

Please don’t hesitate to reach out if you have any questions about the challenge. Good luck!

