package ch.unsustainable.xxe;

import java.io.File;

import java.io.IOException;
import java.io.PrintWriter;
import java.util.HashMap;
import java.util.List;
import java.util.zip.*;

import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.commons.fileupload.FileItem;
import org.apache.commons.fileupload.disk.DiskFileItemFactory;
import org.apache.commons.fileupload.servlet.ServletFileUpload;

import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathFactory;

import org.w3c.dom.Document;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.DocumentBuilder;

/**
 * Servlet implementation class Xxe
 */
@WebServlet("/Xxe")
public class Xxe extends HttpServlet {
	private static final long serialVersionUID = 1L;

	private static final int MEMORY_THRESHOLD = 1024 * 1024 * 3; // 3MB
	private static final int MAX_FILE_SIZE = 1024 * 1024 * 40; // 40MB
	private static final int MAX_REQUEST_SIZE = 1024 * 1024 * 50; // 50MB

	/**
	 * Upon receiving file upload submission, parses the request to read upload
	 * data and saves the file on disk.
	 */
	protected void doPost(HttpServletRequest request,
			HttpServletResponse response) throws ServletException, IOException {
		// checks if the request actually contains upload file
		if (!ServletFileUpload.isMultipartContent(request)) {
			// if not, we stop here
			PrintWriter writer = response.getWriter();
			writer.println("Error: Form must has enctype=multipart/form-data.");
			writer.flush();
			return;
		}

		// configures upload settings
		DiskFileItemFactory factory = new DiskFileItemFactory();
		// sets memory threshold - beyond which files are stored in disk
		factory.setSizeThreshold(MEMORY_THRESHOLD);
		// sets temporary location to store files
		factory.setRepository(new File(System.getProperty("java.io.tmpdir")));

		ServletFileUpload upload = new ServletFileUpload(factory);

		// sets maximum size of upload file
		upload.setFileSizeMax(MAX_FILE_SIZE);

		// sets maximum size of request (include file + form data)
		upload.setSizeMax(MAX_REQUEST_SIZE);

		try {
			List<FileItem> formItems = upload.parseRequest(request);

			if (formItems != null && formItems.size() > 0) {
				// iterates over form's fields
				for (FileItem item : formItems) {
					// processes only fields that are not form fields
					if (!item.isFormField()) {
						ZipInputStream zipIn = new ZipInputStream(
								item.getInputStream());
						ZipEntry entry = null;
						while ((entry = zipIn.getNextEntry()) != null) {
							if (entry.getName().equals("content.xml")) {
								DocumentBuilderFactory dbFactory = DocumentBuilderFactory
										.newInstance();
							
								//dbFactory.setFeature("http://apache.org/xml/features/disallow-doctype-decl", true);
								dbFactory.setNamespaceAware(true);
								DocumentBuilder dBuilder = dbFactory
										.newDocumentBuilder();
							
								Document doc = dBuilder.parse(zipIn);

								XPathFactory xPathFactory = XPathFactory
										.newInstance();
								XPath xpath = xPathFactory.newXPath();
								HashMap<String, String> prefMap = new HashMap<String, String>() {
									/**
									 * 
									 */
									private static final long serialVersionUID = 1L;

									{
										put("office",
												"urn:oasis:names:tc:opendocument:xmlns:office:1.0");
										put("table",
												"urn:oasis:names:tc:opendocument:xmlns:table:1.0");

										put("text",
												"urn:oasis:names:tc:opendocument:xmlns:text:1.0");

									}
								};
								SimpleNamespaceContext namespaces = new SimpleNamespaceContext(
										prefMap);
								xpath.setNamespaceContext(namespaces);

								XPathExpression expr = xpath
										.compile("/office:document-content/office:body/office:spreadsheet/table:table");
								Node node = (Node) expr.evaluate(doc,
										XPathConstants.NODE);
								StringBuilder htmlReturn = new StringBuilder();

								for (int i = 0; i < node.getChildNodes()
										.getLength(); i++) {
									htmlReturn.append("<tr>");
									NodeList row = node.getChildNodes().item(i)
											.getChildNodes();
									for (int c = 0; c < row.getLength(); c++) {
										htmlReturn.append("<td>");
										Node cell = row.item(c);
										Node textNode = cell.getFirstChild();
										if (textNode != null)
											htmlReturn.append(textNode
													.getTextContent());
										htmlReturn.append("</td>");
									}
									htmlReturn.append("</tr>");
								}
								
								request.setAttribute("table",
										String.format("<table class='table table-bordered'>%s</table>", htmlReturn.toString()));
								break;
							}

						}

					}
				}

			}
		} catch (Exception ex) {
			request.setAttribute("message",
					"An Error ocurred: " + ex.getMessage());
		}
		// redirects client to message page
		getServletContext().getRequestDispatcher("/upload.jsp").forward(
				request, response);
	}


	protected void doGet(HttpServletRequest request,
			HttpServletResponse response) throws ServletException, IOException {
		request.getRequestDispatcher("upload.jsp").forward(request, response);
	}
}
